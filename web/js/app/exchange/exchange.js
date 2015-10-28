require(['../../config'],function(){
    require(['common']);
    require(['jquery', 'router', 'routing'],function($, router, routing){
        $("#amazonBtn").click(function(){
            return false;
            $('#exchangeForm').attr("action", Routing.generate("_exchange_amazonInfo"));
            $("#exchangeForm").submit();
        });
        $("#alipayBtn").click(function(){
            alipayClick();
        });
        $("#mobileBtn").click(function(){
            $('#exchangeForm').attr("action", Routing.generate("_exchange_mobileInfo"));
            $("#exchangeForm").submit();
        });
        $("#flowBtn").click(function(){
            $('#exchangeForm').attr("action", Routing.generate("_exchange_flowInfo"));
            $("#exchangeForm").submit();
        });
        function alipayClick(){
            $.ajax({
                url: Routing.generate("_exchange_issetIdent"),
                post: "GET",
                success:function(data){
                  if(data==1){
                    $('#exchangeForm').attr("action", Routing.generate("_exchange_alipayInfo"));
                    $("#exchangeForm").submit();
                  }else{
                    $('#exchangeForm').attr("action", Routing.generate("_exchange_identityCardComfirm",{"type": "alipay"}));
                    $("#exchangeForm").submit();
                  }
                }
            });
        }
    });
});