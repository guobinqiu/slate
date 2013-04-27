<!doctype html>
<html>
<head>
<meta charset="UTF-8" />
<title></title>
<!-- [if lt IE9] -->
<script src="js/html5.js" type="text/javascript"></script>
<!-- [endif] -->
<link href="../../public/css/default.css" rel="stylesheet" type="text/css" />
<link href="../../public/css/index.css" rel="stylesheet" type="text/css" />
<script src="../../public/js/jquery-1.7.js" type="text/javascript"></script>
<script src="../../public/js/banner.js" type="text/javascript"></script>
</head>
<body>
<header>
  <section>
    <div id="logo"><a href="#"><img src="images/logo.gif" width="109" height="37"></a></div>
    <nav id="menu">
      <ul>
        <li><a href="#">��ҳ</a></li>
        <li><a href="#">������</a></li>
        <li><a href="#">�һ�����</a></li>
        <li><a href="#">��������</a></li>
        <li class="last"><a href="#">��������</a></li>
      </ul>
    </nav>
    <nav id="user">
    	<a class="log" href="#">��¼</a><a class="reg" href="#">ע��</a>
    </nav>
  </section>
</header>
<div class="webArticle index">
	<section class="one">
        <div id="banner">
        	<ul class="bannerImg">
            	<li><img src="other/banner1.jpg" width="722" height="250"></li>
                <li><img src="other/banner1.jpg" width="722" height="250"></li>
                <li><img src="other/banner1.jpg" width="722" height="250"></li>
                <li><img src="other/banner1.jpg" width="722" height="250"></li>
            </ul>
            <div class="bannerNumber"><a href="#" class="hover">1</a><a href="#">2</a><a href="#">3</a><a href="#">4</a></div>
        </div>
        <div id="userInformation"></div>
    </section>
    <section class="process"><img src="images/process.jpg" width="960" height="85"></section>
  <section class="main">
    	<article>
        	<h2><img src="images/hotTask.jpg" width="209" height="19" alt="��������"><a class="more" href="#">����>></a></h2>
        	
        	{% for item in advertise %}
               <div class="task">
               <a href="#">{{ item.content }}</a>
                <h3>{{ item.title }}</h3>
                <p>��������<span>500</span>����</p>
            </div>
            {% endfor %}
        	
    </article>
    	<aside>
        	<section>
            	<h4>����<a class="more" href="#">����>></a></h4>
              <ul class="news">
                	<li><a href="#"><span>�����</span>����Ϸ���塱��4����ʼ����</a></li>
                    <li><a href="#"><span>�����</span>����Ϸ���塱��4����ʼ����</a></li>
                    <li><a href="#"><span>�����</span>����Ϸ���塱��4����ʼ����</a></li>
                    <li><a href="#"><span>�����</span>����Ϸ���塱��4����ʼ����</a></li>
                    <li><a href="#"><span>�����</span>����Ϸ���塱��4����ʼ����</a></li>
                    <li class="last"><a href="#"><span>�����</span>����Ϸ���塱��4����ʼ����</a></li>
                </ul>
            </section>
            <section>
            	<h4>���¶һ�<a class="more" href="#">����>></a></h4>
              <ul class="timeline">
                	<li><a href="#">14896630</a>�һ��ƶ�����<font>50</font>Ԫֱ�廰</li>
                    <li><a href="#">14896630</a>�һ��ƶ�����<font>50</font>Ԫֱ�廰</li>
                    <li><a href="#">14896630</a>�һ��ƶ�����<font>50</font>Ԫֱ�廰</li>
                    <li><a href="#">14896630</a>�һ��ƶ�����<font>50</font>Ԫֱ�廰</li>
                    <li><a href="#">14896630</a>�һ��ƶ�����<font>50</font>Ԫֱ�廰</li>
                    <li><a href="#">14896630</a>�һ��ƶ�����<font>50</font>Ԫֱ�廰</li>
                    <li><a href="#">14896630</a>�һ��ƶ�����<font>50</font>Ԫֱ�廰</li>
                    <li><a href="#">14896630</a>�һ��ƶ�����<font>50</font>Ԫֱ�廰</li>
                    <li><a href="#">14896630</a>�һ��ƶ�����<font>50</font>Ԫֱ�廰</li>
                    <li class="last"><a href="#">14896630</a>�һ��ƶ�����<font>50</font>Ԫֱ�廰</li>
                </ul>
            </section>
        </aside>
  </section>
</div>
<footer>
	<section><a href="#">���ڲʺ籴��</a>|<a href="#">��������</a>|<a href="#">������</a>|<a href="#">�����̼�</a>|<span>�ʺ籴����Ȩ���� @2013</span>
	</section>
</footer>
</body>
</html>