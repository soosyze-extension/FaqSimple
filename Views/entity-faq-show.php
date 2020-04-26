
<?php foreach ($entities as $keyEntity => $entity): ?>
    <div class="faq">
        <?php foreach ($entity as $keyField => $field): ?>
            <?php if ($field[ 'field_show_label' ]): ?>
                <h2><?php echo $field[ 'field_label' ]; ?></h2>
            <?php endif; ?>
            <?php if ($keyField == 'question'): ?>
                <div class="faq_head" onclick="toogle_faq('<?php echo "response-$keyEntity"; ?>')">
                    <h3> <i class="fa fa-plus"></i> <?php echo $field[ 'field_value' ]; ?></h3>
                </div>
            <?php endif; ?>
            <?php if ($keyField == 'response'): ?>
                <div id="<?php echo "response-$keyEntity"; ?>" class="faq_content" style="display:none">
                    <?php echo $field[ 'field_display' ]; ?>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
<?php endforeach; ?>
