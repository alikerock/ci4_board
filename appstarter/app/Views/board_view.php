<h3 class="pb-4 mb-4 fst-italic border-bottom text-center">
  - 게시판 보기 -
</h3>

<article class="blog-post">
  <h2 class="blog-post-title"><?php echo $view->subject;?></h2>
  <p class="blog-post-meta"><?php echo $view->regdate;?> by <a href="#"><?php echo $view->userid;?></a></p>
  <hr>
  <p>
    <?php echo $view->content;?>
  </p>
  <hr>
</article>