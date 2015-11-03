require(['../../config'],function(){
    require(['common']);
    require(['jquery', 'routing'],function($, routing){
        var exchangeForm = $('#exchangeForm');
        var submitBtns = $('.exchangeBtn a');
        submitBtns.on('click', function(){
            var str = $(this).attr('id');
            switch(str) {
                case 'amazonBtn':
                    return false;
                    exchangeForm.attr("action", Routing.generate("_exchange_amazonInfo")); 
                    exchangeForm.submit(); break;
                case 'alipayBtn':
                    alipayClick(); break;
                case 'mobileBtn':
                    exchangeForm.attr("action", Routing.generate("_exchange_mobileInfo"));
                    exchangeForm.submit(); break;
                case 'flowBtn':
                    exchangeForm.attr("action", Routing.generate("_exchange_flowInfo"));
                    exchangeForm.submit(); break;
                default: break;
            }
        });
        function alipayClick(){
            $.ajax({
                url: Routing.generate("_exchange_issetIdent"),
                post: "GET",
                success:function(data){
                  // if(data==1){
                    exchangeForm.attr("action", Routing.generate("_exchange_alipayInfo"));
                  // }else{
                  //   exchangeForm.attr("action", Routing.generate("_exchange_identityCardComfirm",{"type": "alipay"}));
                  // }
                  exchangeForm.submit();
                }
            });
        }
    });
});