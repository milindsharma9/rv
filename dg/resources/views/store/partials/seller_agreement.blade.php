<div class="cms_content">
    <?php 
        if (!empty($cmsData->description)) {
            if (!empty($storeDetails)) {
                echo str_replace(
                        array(
                            "@COMPANY_NAME@",
                            "@COMPANY_NUMBER@",
                            "@COMPANY_ADDRESS@",
                            "@STORE_NAME@",
                            "@STORE_ADDRESS@"
                        ),
                        array(
                            $storeDetails['company_name'],
                            $storeDetails['company_number'],
                            $storeDetails['company_address'],
                            $storeDetails['store_name'],
                            $storeDetails['store_address'],
                        ),
                        $cmsData->description);
            } else {
                echo $cmsData->description;
            }
        } 
    ?>
</div>