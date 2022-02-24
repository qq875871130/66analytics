<?php defined('ALTUMCODE') || die() ?>

<div>
    <p class="text-muted"><?= language()->admin_settings->cache->help ?></p>

    <label for="size"><?= language()->admin_settings->cache->size ?></label>
    <div class="input-group mb-3">
        <input id="size" name="size" type="text" class="form-control form-control-lg" value="<?= \Altum\Cache::$adapter->getStats()->getSize() / 1000 / 1000 ?>" readonly="readonly" />
        <div class="input-group-append">
            <span class="input-group-text">
                MB
            </span>
        </div>
    </div>

</div>

<button type="submit" name="submit" class="btn btn-lg btn-block btn-primary mt-4"><?= language()->admin_settings->cache->clear ?></button>
