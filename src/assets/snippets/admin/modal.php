
    <?php if(!empty($_COOKIE['message'])):
        $cookies=explode('/',$_COOKIE['message']); 
        foreach($cookies as $message):?> 
            <div class="modal_notification" id="modal">
                <div>
                    <button class="button" >
                        <i class="uil uil-times"></i>
                    </button>
            </div>
                <p class="modal_text">
                    <?=$message?> 
                </p>
            </div>
        <?php endforeach;?>
    <?php endif;?>
 