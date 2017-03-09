$(document).ready(function() {
    $("#goto").click(function(){
      var url = window.location.href;
      url = url.split("?page=");
      var currentPage = $("#page").val();
      var first = $("#first").val();
      var pageCount = $("#pageCount").val() ;
      if (!currentPage || currentPage  <= first ){
          currentPage = first;
          window.location.href = url[0];
      }
      if(currentPage >= pageCount ){
          currentPage = pageCount;
          window.location.href = url[0]+'?page='+pageCount;
      }
      if(currentPage > first && currentPage < pageCount )
          window.location.href = url[0]+'?page='+currentPage;
      
    });
});