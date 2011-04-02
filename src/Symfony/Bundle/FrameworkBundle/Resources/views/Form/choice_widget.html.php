<?php if ($expanded): ?>
    <?php foreach ($renderer as $choice => $child): ?>
        <?php echo $child->getWidget() ?>
        <?php echo $child->getLabel() ?>
    <?php endforeach ?>
<?php else: ?>
    <select
        id="<?php echo $id ?>"
        name="<?php echo $name ?>"
        <?php if ($read_only): ?> disabled="disabled"<?php endif ?>
        <?php if ($multiple): ?> multiple="multiple"<?php endif ?>
        <?php if ($class): ?> class="<?php echo $class ?>"<?php endif ?>
        <?php if (isset($attr)): echo $renderer->getTheme()->attributes($attr); endif; ?>
    >
        <?php if (!$required): ?><option value=""><?php echo $empty_value; ?></option><?php endif; ?>
        <?php if (count($preferred_choices) > 0): ?>
            <?php foreach ($preferred_choices as $choice => $label): ?>
                <?php if ($renderer->isChoiceGroup($label)): ?>
                    <optgroup label="<?php echo $choice ?>">
                        <?php foreach ($label as $nestedChoice => $nestedLabel): ?>
                            <option value="<?php echo $nestedChoice ?>"<?php if ($renderer->isChoiceSelected($nestedChoice)): ?> selected="selected"<?php endif?>><?php echo $nestedLabel ?></option>
                        <?php endforeach ?>
                    </optgroup>
                <?php else: ?>
                    <option value="<?php echo $choice ?>"<?php if ($renderer->isChoiceSelected($choice)): ?> selected="selected"<?php endif?>><?php echo $label ?></option>
                <?php endif ?>
            <?php endforeach ?>
            <option disabled="disabled"><?php echo $separator ?></option>
        <?php endif ?>
        <?php foreach ($choices as $choice => $label): ?>
            <?php if ($renderer->isChoiceGroup($label)): ?>
                <optgroup label="<?php echo $choice ?>">
                    <?php foreach ($label as $nestedChoice => $nestedLabel): ?>
                        <option value="<?php echo $nestedChoice ?>"<?php if ($renderer->isChoiceSelected($nestedChoice)): ?> selected="selected"<?php endif?>><?php echo $nestedLabel ?></option>
                    <?php endforeach ?>
                </optgroup>
            <?php else: ?>
                <option value="<?php echo $choice ?>"<?php if ($renderer->isChoiceSelected($choice)): ?> selected="selected"<?php endif?>><?php echo $label ?></option>
            <?php endif ?>
        <?php endforeach ?>
    </select>
<?php endif ?>