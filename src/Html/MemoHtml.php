<?php

namespace Bbs\Html;

use Bbs\Response\HtmlResponse;

class MemoHtml extends HtmlResponse
{
    /** @var MemoDto */
    private $vars;
    public function __construct(MemoDto $vars)
    {
        $this->vars = $vars;
    }

    public function showHtml(): void
    {
$message = $this->vars->message;
$form = $this->vars->form;
$rows = $this->vars->articles;

?>

<!DOCTYPE html>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1, user-scalable=0" >
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
    <input type="hidden" name="article_id" value="<?= $form->article_id ?>">
    タイトル: <input type="text" name="title" value="<?= h($form->title) ?>"><br>
    本　　文: <textarea name="body" value=""><?= h($form->body) ?></textarea><br>
    タ　　グ: <input type="text" name="tags" value="<?= h($form->tags) ?>"><br>
    <button type="submit">投稿</button>

  </form>

  <?php if ($form->article_id): ?>
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
        <time>created: <?= date('Y-m-d H:i:s', strtotimeex($row->created_at)) ?></time>
        <?php if ($row->updated_at): ?>
          <br>
          <time>updated: <?= date('Y-m-d H:i:s', strtotimeex($row->updated_at)) ?></time>
        <?php endif; ?>
      </div>
      <div>
        <?php foreach (explode(' ', $row->tags ?: '') as $tag): ?>
          <a href="?tag=<?= rawurlencode($tag) ?>"><?= h($tag) ?></a>
        <?php endforeach ?>
      </div>

      <hr>
      <a href="/<?= $row->article_id ?>">編集</a>
      <form action="/<?= $row->article_id ?>/delete" method="POST" style="margin-left: 1em;">
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


<?php
    }
}
