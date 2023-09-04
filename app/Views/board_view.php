<h3 class="pb-4 mb-4 fst-italic border-bottom text-center">
  - 게시판 보기 -
</h3>

<?php 
var_dump($view); 
?>

<article class="blog-post">
  <h2 class="blog-post-title"><?php echo $view->subject;?></h2>
  <p class="blog-post-meta"><?php echo $view->regdate;?> by <a href="#"><?php echo $view->userid;?></a></p>
  <hr>
  <p>
    <?php echo $view->content;?>
  </p>
  <?php
        if(isset($view->fs)){
          $vfs = explode(",",$view->fs); //파일 정보를 ,를 이용하여 배열로 분리
          foreach($vfs as $img){
            if(isset($img)){
          ?>
          <img src="<?php echo  base_url('/uploads/'.$img);?>">
          <?php
            }
          }
        }?>
  <hr>
  <p class="text-end">
    <?php
      if(isset($_SESSION['userid'])){ //로그인후 세션에 userid 정보가 있다면
    ?>
    <a href="/modify/<?php echo $view->bid;?>" class="btn btn-primary">수정<a>
        <a href="/delete/<?php echo $view->bid;?>" class="btn btn-warning">삭제<a>
            <?php } ?>
            <a href="/board" class="btn btn-primary">목록<a>
  </p>
</article>