<form method="post" action="<?= site_url('/writeSave') ?>" enctype="multipart/form-data">
  <div class="mb-3">
    <label for="exampleFormControlInput1" class="form-label">이름</label>
    <input type="text" name="username" class="form-control" id="exampleFormControlInput1" placeholder="이름을 입력하세요."
      value="<?php echo $_SESSION['username']?>">
  </div>
  <div class="mb-3">
    <label for="exampleFormControlInput1" class="form-label">제목</label>
    <input type="text" name="subject" class="form-control" id="exampleFormControlInput1" placeholder="제목을 입력하세요."
      value="<?php echo isset($view->subject)?$view->subject:'';?>">
  </div>
  <div class="mb-3">
    <label for="exampleFormControlTextarea1" class="form-label">내용</label>
    <textarea class="form-control" id="exampleFormControlTextarea1" name="content"
      rows="3"><?php echo isset($view->content)?$view->content:'';?></textarea>
  </div>
  <div class="mb-3">
    <input type="file" name="upfile" id="upfile" class="form-control form-control-lg"
      aria-label="Large file input example">
  </div>
  <br>
  <?php
    $btntitle=isset($view->bid)?"수정":"등록";
  ?>
  <button type="submit" class="btn btn-primary"><?php echo $btntitle;?></button>

</form>