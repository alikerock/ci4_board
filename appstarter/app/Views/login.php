<?php if ($alert = session('alert')) : ?>
<div class="alert alert-warning"><?= $alert ?></div>
<?php endif ?>

<form class="row g-3 needs-validation" action="<?php echo base_url(); ?>loginok" method="post">

  <div class="col-12">
    <label for="validationCustom02" class="form-label">아이디</label>
    <input type="text" class="form-control" id="userid" name="userid" placeholder="" required>
  </div>
  <div class="col-12">
    <label for="validationCustom02" class="form-label">비밀번호</label>
    <input type="password" class="form-control" id="passwd" name="passwd" placeholder="" required>
  </div>

  <div class="col-12">
    <button class="btn btn-primary" type="submit">로그인</button>
  </div>
</form>