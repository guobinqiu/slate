$(document).ready(function(){
	
  var page = 1;
  var pageNum = $(".cpsBanner li").length;
  
  $(".moveDiv").width(pageNum * $(".cpsBanner li").width())
  
  var goPage = function(){
	  $(".moveDiv").animate({ left: -(page-1)*($(".cpsBanner li").width())}, 500);
  }
  var nextPage = function(){
	clearTimeout(t)
    if(page >= pageNum ){
		page = 1	
	}else{
		page = page + 1
	}
	goPage();
	t = setInterval(nextPage,3000)
  }
  $(".cpsBanner .prevBtn").click(function(){
	clearTimeout(t)
	if(page <= 1 ){
		page = pageNum	
	}else{
		page = page - 1
  	}
	goPage();
	t = setInterval(nextPage,3000)
  })
  $(".cpsBanner .nextBtn").click(function(){
	nextPage();
  })
  
  var t = setInterval(nextPage,3000)
  
});