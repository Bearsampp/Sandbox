<a class="anchor" name="memcached"></a>
<div class="row-fluid">
  <div class="col-lg-12">
    <h1><img src="<?php echo $bearsamppHomepage->getResourcesPath() . '/img/memcached.png'; ?>" /> <?php echo $bearsamppLang->getValue(Lang::MEMCACHED); ?> <small></small></h1>
  </div>
</div>
<div class="row-fluid">
  <div class="col-lg-6">
    <div class="list-group">
      <span class="list-group-item memcached-checkport">
        <?php echo $getLoader; ?>
        <i class="fa-solid fa-server"></i> <?php echo $bearsamppLang->getValue(Lang::STATUS); ?>
      </span>
      <span class="list-group-item memcached-versions">
              <span class="label-left col-1">
                <i class="fa-solid fa-code-merge"></i> <?php echo $bearsamppLang->getValue(Lang::VERSIONS); ?>
              </span>
              <span class="memcached-version-list float-right col-11">
                <?php echo $getLoader; ?>
              </span>
      </span>
    </div>
  </div>
</div>
