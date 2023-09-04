<table class="table">
  <thead>
    <tr>
      <th scope="col">번호</th>
      <th scope="col">글쓴이</th>
      <th scope="col">제목</th>
      <th scope="col">등록일</th>
    </tr>
  </thead>
  <tbody id="board_list">
    <?php
      foreach($list as $ls){    
    ?>
    <tr>
      <th scope="row"><?php echo $ls->bid;?></th>

      <td><?php echo $ls->userid;?></td>
      <td><a href="/boardView/<?php echo $ls->bid;?>"><?php echo $ls->subject;?></a></td>
      <td><?php echo $ls->regdate;?></td>
    </tr>
    <?php }?>
  </tbody>

</table>
<!-- 페이징 링크 표시 -->
<div>전체 게시물수:<?= $total ?></div>
<div>현재페이지:<?= $page ?></div>
<div>페이지당 게시물수:<?= $perPage ?></div>
<div class="pager-links">
        <?= $pager_links ?>
</div>

<p class="text-end">
  <a href="/boardWrite" class="btn btn-primary">등록</a>

  <?php
    if(isset($_SESSION['userid'])){
    ?>
    <a href="/logout" class="btn btn-warning">로그아웃<a>
    
    <?php }else{?>    
      <a href="/login" class="btn btn-warning">로그인<a>
    <?php }?>
</p>