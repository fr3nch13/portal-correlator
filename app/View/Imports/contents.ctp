<?php ?>
<div class="like_pre">
<?php
$content = $this->Wrap->highlightVectors($content, $vectors);
$content = str_replace("\n", "\n<br />", $content);
echo $content;
?>
</div>