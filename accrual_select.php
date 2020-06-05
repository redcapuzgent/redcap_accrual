<?php
global $module;

/**
 * @var $module \uzgent\AccrualReport\AccrualReport
 */

?>
<H2>
    Select a date field to base your accrual report on.
</H2>
<p>
<form action="<?php echo $module->getProcessAccrualURL(); ?>" method="get">
    <select name="datefield">
        <?php

            foreach(REDCap::getFieldNames() as $fieldname)
            {
                echo "<option value='$fieldname'>".$fieldname."</option>";
            }
        ?>

    </select>
    <input type="hidden" name="page" value="<?php echo "accrual";?>"/>
    <input type="hidden" name="pid" value="<?php echo $_GET["pid"];?>"/>
    <input type="hidden" name="prefix" value="<?php echo $_GET['prefix'];?>"/>
    <input class="btn btn-primary" type="submit">
</form>
</div>
<BR/>


<?php
