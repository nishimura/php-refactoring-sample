<?php

ini_set('display_errors', true);
error_reporting(-1);

/** @param ?string $a */
function h($a):string
{
    return htmlspecialchars($a, ENT_QUOTES, 'UTF-8');
}

/** @param string */
function br($a): string
{
    return nl2br(h($a));
}

/** @param string|array<mixed,mixed>|null $a */
function maybe_string(&$a):?string
{
    if ($a === null)
        return null;
    $s = filter_var($a);
    if ($s === false || $s === '')
        return null;
    return $s;
}

/** @param string|array<mixed,mixed>|null $a */
function maybe_int(&$a): ?int
{
    if ($a === null)
        return null;

    $i = filter_var($a, FILTER_VALIDATE_INT);
    if ($i === false)
        return null;

    return (int)$i;
}

function getPdo(): PDO
{
    $data = dirname(__DIR__) . '/data/db.sqlite';
    $pdo = new PDO("sqlite:$data");

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);

    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    return $pdo;
}

$article = (object)[
    'article_id' => null,
    'title' => null,
    'body' => null,
    'created_at' => null,
    'updated_at' => null,
    'tags' => null,
];

$pdo = getPdo();
$message = null;
if (($title = maybe_string($_POST['title'])) &&
    ($body = maybe_string($_POST['body']))){

    if ($articleId = maybe_int($_POST['article_id'])){
        $sql = 'update article set title = ?, body = ?, updated_at = current_timestamp where article_id = ?';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$title, $body, $articleId]);

        $pdo->exec(sprintf('delete from tag where article_id = %d', $articleId));

        $message = '更新しました。';

    }else{
        $sql = 'insert into article(title, body) values(?,?)';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$title, $body]);
        $articleId = $pdo->lastInsertId();

        $message = '登録しました。';
    }

    $tags = explode(' ', $_POST['tags'] ?? []);
    if ($tags){
        $tagSql = 'insert into tag values(?, ?)';
        $stmt = $pdo->prepare($tagSql);
        foreach ($tags as $tag){
            $stmt->execute([$articleId, $tag]);
        }
    }

}

if ($id = maybe_int($_POST['delete_id'])){
    $pdo->exec(sprintf('delete from tag where article_id = %d', $id));
    $pdo->exec(sprintf('delete from article where article_id = %d', $id));
    $message = '削除しました。';
}

if ($id = maybe_int($_GET['id'])){
    $sql = "
select article.*, group_concat(tag, ' ') as tags
from article
left outer join tag using(article_id)
where article_id = ?
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    $rows = $stmt->fetchAll();

    if ($rows){
        $article = $rows[0];
    }
}

$tagWhere = '';
$params = [];
if ($search = maybe_string($_GET['tag'])){
    $tagWhere = '
where exists (
  select 1
  from tag
  where article_id = article.article_id
    and tag = ?
)';
    $params = [$search];
}
$stmt = $pdo->prepare(sprintf("
select article.*, tags
from article
left outer join (
  select article_id, group_concat(tag, ' ') as tags
  from tag
  group by article_id
) tag using(article_id)
%s
order by created_at desc
    ", $tagWhere));
$stmt->execute($params);
$rows = $stmt->fetchAll();

?>
<!DOCTYPE html>
<meta charset="UTF-8">
<title>メモ帳</title>
<style type="text/css">
* { vertical-align: middle; }
input, textarea { width: 20em; }
textarea { height: 5em; }
.message { color: red; }
.memo { padding: 1em; margin: 1em; background-color: #FFE; border: 1px solid #DDD; }
form { display: inline-block; }
</style>
<h1>メモ帳</h1>

<?php if ($message): ?>
<section class="message">
  <?= h($message) ?>
</section>
<?php endif; ?>

<section>
  <h2>新規投稿</h2>
  <form action="" method="POST">
    <input type="hidden" name="article_id" value="<?= $article->article_id ?>">
    タイトル: <input type="text" name="title" value="<?= h($article->title) ?>"><br>
    本　　文: <textarea name="body" value=""><?= h($article->body) ?></textarea><br>
    タ　　グ: <input type="text" name="tags" value="<?= h($article->tags) ?>"><br>
    <button type="submit">投稿</button>

  </form>

  <?php if ($article->article_id): ?>
  <div>
    <br>
    <a href="/">キャンセル</a>
  </div>
  <?php endif; ?>
</section>

<hr>

<?php if ($rows): ?>
  <section>
  <h2>メモ一覧</h2>
  <?php foreach ($rows as $row): ?>
    <section class="memo">
      <h3><?= h($row->title) ?></h3>
      <div>
        <?= br($row->body) ?>
      </div>
      <div>
        <time>created: <?= date('Y-m-d H:i:s', strtotime($row->created_at)) ?></time>
        <?php if ($row->updated_at): ?>
          <br>
          <time>updated: <?= date('Y-m-d H:i:s', strtotime($row->updated_at)) ?></time>
        <?php endif; ?>
      </div>
      <div>
        <?php foreach (explode(' ', $row->tags) as $tag): ?>
          <a href="?tag=<?= rawurlencode($tag) ?>"><?= h($tag) ?></a>
        <?php endforeach ?>
      </div>

      <hr>
      <form action="" method="GET">
        <input type="hidden" name="id" value="<?= $row->article_id ?>">
        <button type="submit">編集</button>
      </form>
      <form action="" method="POST" style="margin-left: 1em;">
        <input type="hidden" name="delete_id" value="<?= $row->article_id ?>">
        <button type="submit" onclick="return confirm('削除しますか？')">削除</button>
      </form>
    </section>
  <?php endforeach; ?>
<?php else: ?>
<section>
  <h2>メモ一覧</h2>
  <p>メモはまだありません</p>
</section>
<?php endif; ?>

<hr>
