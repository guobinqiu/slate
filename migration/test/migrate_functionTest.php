<?php

include(__DIR__.'/../script/config.php');
include(__DIR__.'/../script/FileUtil.php');
include(__DIR__.'/../script/Constants.php');
include( __DIR__.'/../script/migrate_function.php');

Constants::$environment = 'test';

class migrate_functionTest extends PHPUnit_Framework_TestCase
{
  public function test_getJiliConnectionByPanelistId() 
  {

    $fh = tmpfile();
    fwrite($fh,<<<EOD
"panelist_id","jili_id","status_flag","stash_data","updated_at","created_at"
"305","16980","1","NULL","2015-01-07 16:50:12","2015-01-07 16:50:12"
"355","4646","1","NULL","2014-11-25 13:16:39","2014-11-25 13:16:39"
"377","2696","1","NULL","2014-11-23 01:22:44","2014-11-23 01:22:44"
"393","1930","1","NULL","2014-11-22 09:33:04","2014-11-22 09:33:04"
"484","16045","1","NULL","2015-01-03 19:25:06","2015-01-03 19:25:06"
"643","9303","1","NULL","2014-12-04 13:38:23","2014-12-04 13:38:23"
"1010","6860","1","NULL","2014-11-28 22:22:23","2014-11-28 22:22:23"
"1042","7308","1","NULL","2014-11-29 22:18:52","2014-11-29 22:18:52"
"1075","4827","1","NULL","2014-11-25 16:20:58","2014-11-25 16:20:58"
"2230707","61411","1","NULL","2015-11-17 14:18:10","2015-11-17 14:18:10"
"2230709","61424","1","NULL","2015-11-17 16:01:09","2015-11-17 16:01:09"
"2230733","61417","1","NULL","2015-11-17 15:03:50","2015-11-17 15:03:50"
"2230736","61415","1","NULL","2015-11-17 14:50:21","2015-11-17 14:50:21"
"2230752","61420","1","NULL","2015-11-17 15:42:34","2015-11-17 15:42:34"
"2230773","61423","1","NULL","2015-11-17 16:00:10","2015-11-17 16:00:10"
"2230798","61429","1","NULL","2015-11-17 16:54:00","2015-11-17 16:54:00"
"2230806","61430","1","NULL","2015-11-17 17:16:53","2015-11-17 17:16:53"
"2230815","61426","1","NULL","2015-11-17 16:36:06","2015-11-17 16:36:06"
"2230842","61432","1","NULL","2015-11-17 17:32:05","2015-11-17 17:32:05"
EOD
    );

    $current  = array();
    $return = getJiliConnectionByPanelistId($fh, '305', $current);
    $this->assertEquals(1, $return['matched'],' the input panelist_id  305 is matched');
    $this->assertEquals(16980, $return['jili_id'], 'the jili_id is 16980 of panelist_id 305');
    $this->assertEquals(305, $return['panelist_id'],' the file hanlder current point to  305 ');

    $return = getJiliConnectionByPanelistId($fh , '',$return);
    $this->assertEquals(0, $return['matched'],'null input panelis_id');
    $this->assertEquals(305, $return['panelist_id'],' keep 305');
    $this->assertEquals(16980, $return['jili_id'], 'keep 16980');

    $return = getJiliConnectionByPanelistId($fh ,371,$return);
    $this->assertEquals(0, $return['matched'],'the input panelist_id  371 is not exists');
    $this->assertEquals(377, $return['panelist_id'],' the  current point moved to  377 ');
    $this->assertEquals(2696, $return['jili_id'], 'keep 16980');

    $return = getJiliConnectionByPanelistId($fh ,355 , $return);
    $this->assertEquals(0, $return['matched'],'the input panelist_id  355 is passed arealdy');
    $this->assertEquals(377, $return['panelist_id'],' keep 377 ');
    $this->assertEquals(2696, $return['jili_id'], 'keep 16980');

    $return = getJiliConnectionByPanelistId($fh, 393, $return);
    $this->assertEquals(1, $return['matched'],'the input panelist_id  393 is passed arealdy');
    $this->assertEquals(393, $return['panelist_id'],'return panelist_id  393 ');
    $this->assertEquals(1930, $return['jili_id'], 'return jili_id 1930');

    fclose($fh);
  }

  function test_getUserWenwenCrossById() 
  {

      $this->markTestIncomplete(
        'This test has not been implemented yet.'
      );


    $fh = tmpfile();
    fwrite($fh, <<<EOD
"id","user_id","created_at","email"
"1","91","2014-11-17 14:56:30","xujf@voyagegroup.com.cn"
"2","1051021","2014-11-17 14:58:23","miaomiao.zhang@d8aspring.com"
"3","110","2014-11-17 15:05:10","takafumi_sekiguchi@researchpanelasia.com"
"4","1206052","2014-11-20 16:53:54","2442092961@qq.com"
"5","1264810","2014-11-20 16:55:45","704617264@qq.com"
"6","1257149","2014-11-20 16:55:50","515776213@qq.com"
"7","1267542","2014-11-20 16:56:05","2605990968@qq.com"
"8","1085696","2014-11-20 16:56:44","tangqing1984@126.com"
"9","1266832","2014-11-20 16:57:23","1627958274@qq.com"
"61424","1437347","2015-11-17 15:01:04","58073288@qq.com"
"61425","1437413","2015-11-17 15:21:08","383589666@qq.com"
"61426","1437443","2015-11-17 15:36:02","z863437758@163.com"
"61427","1436474","2015-11-17 15:52:19","hailong.719@163.com"
"61428","1437325","2015-11-17 15:52:32","604124403@qq.com"
"61429","1437428","2015-11-17 15:53:57","allykua66@163.com"
"61430","1437434","2015-11-17 16:16:49","lieyanhanbing810@163.com"
"61431","1436638","2015-11-17 16:29:11","861522677@qq.com"
"61432","1437472","2015-11-17 16:32:01","dfln@qq.com"
"61433","1421745","2015-11-17 16:40:33","854799320@qq.com"
EOD
  );

    $return = getUserWenwenCrossById($fh, '', array());
    $this->assertEquals(0, $return['matched']);

    $return = getUserWenwenCrossById($fh, '1', $return);
    $this->assertEquals(1, $return['matched']);
    $this->assertEquals(1, $return['id']);
    $this->assertEquals('xujf@voyagegroup.com.cn', $return['email']);


    $return = getUserWenwenCrossById($fh, '', $return);
    $this->assertEquals(0, $return['matched']);
    $this->assertEquals(1, $return['id']);
    $this->assertEquals('xujf@voyagegroup.com.cn', $return['email']);


    $return = getUserWenwenCrossById($fh, '9', $return);
    $this->assertEquals(1, $return['matched']);
    $this->assertEquals(9, $return['id']);
    $this->assertEquals('1627958274@qq.com', $return['email']);

    $return = getUserWenwenCrossById($fh, '8', $return);
    $this->assertEquals(0, $return['matched'],'already passed');
    $this->assertEquals(9, $return['id']);
    $this->assertEquals('1627958274@qq.com', $return['email']);

    $return = getUserWenwenCrossById($fh, '10', $return);
    $this->assertEquals(0, $return['matched']);
    $this->assertEquals(61424, $return['id']);
    $this->assertEquals('58073288@qq.com', $return['email']);


    $return = getUserWenwenCrossById($fh, '61424', $return);
    $this->assertEquals(1, $return['matched']);
    $this->assertEquals(61424, $return['id']);
    $this->assertEquals('58073288@qq.com', $return['email']);

    fclose($fh);
  }

  function test_getUserWenwenCross() 
  {

    $fh = tmpfile();
    fwrite($fh, <<<EOD
"id","user_id","created_at","email"
"1","91","2014-11-17 14:56:30","xujf@voyagegroup.com.cn"
"2","1051021","2014-11-17 14:58:23","miaomiao.zhang@d8aspring.com"
"3","110","2014-11-17 15:05:10","takafumi_sekiguchi@researchpanelasia.com"
"4","1206052","2014-11-20 16:53:54","2442092961@qq.com"
"5","1264810","2014-11-20 16:55:45","704617264@qq.com"
"6","1257149","2014-11-20 16:55:50","515776213@qq.com"
"7","1267542","2014-11-20 16:56:05","2605990968@qq.com"
"8","1085696","2014-11-20 16:56:44","tangqing1984@126.com"
"9","1266832","2014-11-20 16:57:23","1627958274@qq.com"
"61424","1437347","2015-11-17 15:01:04","58073288@qq.com"
"61425","1437413","2015-11-17 15:21:08","383589666@qq.com"
"61426","1437443","2015-11-17 15:36:02","z863437758@163.com"
"61427","1436474","2015-11-17 15:52:19","hailong.719@163.com"
"61428","1437325","2015-11-17 15:52:32","604124403@qq.com"
"61429","1437428","2015-11-17 15:53:57","allykua66@163.com"
"61430","1437434","2015-11-17 16:16:49","lieyanhanbing810@163.com"
"61431","1436638","2015-11-17 16:29:11","861522677@qq.com"
"61432","1437472","2015-11-17 16:32:01","dfln@qq.com"
"61433","1421745","2015-11-17 16:40:33","854799320@qq.com"

EOD
  );
    $a = getUserWenwenCross($fh);
    $this->assertCount(19, $a );
    $this->assertEquals("xujf@voyagegroup.com.cn", $a[1] );
    $this->assertEquals("854799320@qq.com", $a["61433"] );
    $this->assertEquals("854799320@qq.com", $a[61433] );
    fclose($fh);
  }

  function test_getPointExchangeByPanelistId() 
  {
    $fh = tmpfile();
    fwrite($fh, <<<EOD
"panelist_id","jili_email","status_flag","stash_data","updated_at","created_at"
"305","28216843@qq.com","1","NULL","2014-02-24 10:21:34","2014-02-20 11:58:08"
"307","yanghuafeng126@126.com","1","NULL","2014-08-29 10:07:59","2014-08-27 18:13:35"
"355","shyshe163@163.com","1","NULL","2014-02-10 17:30:19","2014-02-07 13:07:53"
"377","limeiliang@126.com","1","NULL","2014-02-18 09:43:42","2014-02-15 19:44:03"
"393","tangping1202@163.com","1","NULL","2014-02-25 13:24:23","2014-02-22 13:04:42"
"484","zy329465@sina.com","1","NULL","2014-05-07 17:23:46","2014-03-30 02:16:05"
"496","zyq_14@163.com","1","NULL","2014-06-29 00:23:25","2014-06-26 10:52:53"
"643","lixingyunlixingrui@163.com","1","NULL","2014-03-26 11:11:04","2014-03-25 11:13:01"
"816","kajeeyy@sina.com","1","NULL","2014-01-30 13:17:05","2014-01-28 11:13:55"
"2230869","294932134@qq.com","0","{""activation_url"":""https://www.91jili.com/user/setPassFromWenwen/24aa196d65701d2a2d1ef22ae1a002e4/1437492""}","2015-11-17 17:23:12","2015-11-17 17:23:12"
"2230872","525746379@qq.com","0","{""activation_url"":""https://www.91jili.com/user/setPassFromWenwen/42dcebfab20d335b88d0069ddb094b92/1437493""}","2015-11-17 17:27:55","2015-11-17 17:27:55"
"2230873","yxwei286@sina.com","0","{""activation_url"":""https://www.91jili.com/user/setPassFromWenwen/6cd393fc2e38d4317caeb8963a4520f9/1437494""}","2015-11-17 17:28:48","2015-11-17 17:28:48"
"2230874","lulugao2013@126.com","0","{""activation_url"":""https://www.91jili.com/user/setPassFromWenwen/9c3b4cf43494e4cc2602fd7d841b2892/1437495""}","2015-11-17 17:29:01","2015-11-17 17:29:01"
"2230875","510665745@qq.com","0","{""activation_url"":""https://www.91jili.com/user/setPassFromWenwen/48fb58c4e328e075a175e2144bd19dcc/1437496""}","2015-11-17 17:33:41","2015-11-17 17:33:41"
"2230876","745937848@qq.com","0","{""activation_url"":""https://www.91jili.com/user/setPassFromWenwen/c74d6530616d6889e5e55dae029d9d48/1437499""}","2015-11-17 17:37:29","2015-11-17 17:37:29"
"2230877","2535974463@qq.com","0","{""activation_url"":""https://www.91jili.com/user/setPassFromWenwen/8b93a39b12fae057e7e25d6d83f445a5/1437498""}","2015-11-17 17:37:03","2015-11-17 17:37:03"
"2230878","1085658802@qq.com","0","{""activation_url"":""https://www.91jili.com/user/setPassFromWenwen/8f87e8804274087718404d00316d24cd/1437497""}","2015-11-17 17:37:00","2015-11-17 17:37:00"
"2230879","18035443698@163.com","0","{""activation_url"":""https://www.91jili.com/user/setPassFromWenwen/329816003bd75ef6d434d67a55fe00e4/1437500""}","2015-11-17 17:38:44","2015-11-17 17:38:44"
"2230880","13559988133@163.com","0","{""activation_url"":""hreturnttps://www.91jili.com/user/setPassFromWenwen/ad05344a9d760c8f56f6a0d0e2873dda/1437501""}","2015-11-17 17:39:59","2015-11-17 17:39:59"
EOD
  );
    $return = getPointExchangeByPanelistId ($fh, '', array());
    $this->assertEquals(0,$return['matched'] );


    $return = getPointExchangeByPanelistId ($fh, '816', $return);
    $this->assertEquals(1,$return['matched'] );
    $this->assertEquals(816,$return['panelist_id'] );
    $this->assertEquals('kajeeyy@sina.com',$return['jili_email'] );

    $return = getPointExchangeByPanelistId ($fh, '2230880', $return);
    $this->assertEquals(0,$return['matched'] );
    $this->assertEquals(2230880,$return['panelist_id'] );
    $this->assertEquals('13559988133@163.com',$return['jili_email'] );

    fclose($fh);
  }

  function test_getUser() 
  {
    $fh = tmpfile();
    fwrite($fh, <<<EOD
"id","email","pwd","is_email_confirmed","is_from_wenwen","wenwen_user","token","nick","sex","birthday","tel","is_tel_confirmed","province","city","education","profession","income","hobby","personalDes","identity_num","reward_multiple","register_date","last_login_date","last_login_ip","points","delete_flag","is_info_set","icon_path","uniqkey","token_created_at","origin_flag","created_remote_addr","created_user_agent","campaign_code","password_choice"
"1291365"," 1160595417@qq.com","2ef75e7c46e06b90507e4d47780fd8426857c0ab","NULL","NULL","NULL","","QQ懂你","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","1","2015-01-24 10:29:25","2015-01-24 10:29:25","NULL","0","0","1","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL"
"1374309"," 1766995961@qq.com","5ffad97ae3afff80a04060a03cde79e5743e687b","NULL","NULL","NULL","","QQ顾小白","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","1","2015-07-03 16:43:14","2015-07-03 16:43:14","NULL","1","0","1","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL"
"1378657"," 209772454@qq.com","d09930b1d566b15a728eb82bd24402f7b83ecb30","NULL","NULL","NULL","","QQD-Oneight","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","1","2015-07-10 11:01:14","2015-07-10 11:01:14","NULL","1","0","1","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL"
"1379445"," 2245303447@qq.com","639ce910d44d212f20397b86bfa57717b7a22e2d","NULL","NULL","NULL","","QQ丫丫","2","1994-10","","NULL","9","88","NULL","NULL","103","1,2,3,4,7,9,11,12","NULL","NULL","1","2015-07-11 19:10:52","2015-07-11 19:28:07","39.182.89.128","26","0","1","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL"
"1331179"," 2292220927@qq.com","643af5f1dccc9301c97b005a927ffda7fbcdc540","NULL","NULL","NULL","","QQ交予我.","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","1","2015-04-18 15:30:57","2015-04-18 15:30:57","NULL","0","0","1","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL"
"1304351"," 3145737585@qq.com","94f696b7723b22b4dec1b8c2d58a848e236d5059","NULL","NULL","NULL","","QQ小霸王.☀","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","1","2015-02-18 18:28:52","2015-02-18 18:28:52","NULL","0","0","1","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL"
"1277673"," 3165376163@qq.com","a1f243356e9c1f3670b697491b9e42bf61cb1396","NULL","NULL","NULL","","QQ梨落落","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","1","2014-12-15 16:24:18","2014-12-15 16:24:18","NULL","1","1","1","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL"
"1349140"," 3226706849@qq.com","dfa89a28e5ecc965981676629308856009036c35","NULL","NULL","NULL","","QQ联友国旅--刘欢","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","1","2015-05-14 11:30:12","2015-05-14 11:30:12","NULL","0","0","1","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL"
"1325239"," 3244434708@qq.com","e36c7eae682695a81ab3f883e9ab8f11dc3ebda0","NULL","NULL","NULL","","QQ★보고싶다 진짜~★","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","1","2015-04-05 13:43:27","2015-04-05 13:43:27","NULL","0","0","1","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL"
"1378015","z_ch_hui@126.com","NULL","NULL","2","NULL","","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","1","2015-07-09 10:42:54","2015-07-09 10:42:54","NULL","0","NULL","0","NULL","1ff490bcd868afc46523a7bba3aca21173173525","NULL","NULL","NULL","NULL","NULL","NULL"
"1050778","z_j1224@163.com","b2c7101f2856b9692362716467c87bca2cef9c21","NULL","1","NULL","","缘字诀","1","1988-5","13585926135","NULL","1","1","NULL","NULL","103","1,2,3","NULL","NULL","1","2013-08-06 17:24:05","2014-03-01 10:49:15","116.247.87.130","0","NULL","1","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL"
"1257919","z_q_nancy@163.com","1b9ed0d64ac9e60283d0b30b8760247267d34210","NULL","2","NULL","0e1eef00d07f86563dfcc9b50c0e6cb2","prettyzq","2","1986-8","13526810713","NULL","14","164","NULL","NULL","102","1,2,3,4,5,6,7,8,9,10,11,12","NULL","NULL","1","2014-10-28 12:08:31","2014-10-30 12:26:17","49.122.70.12","0","NULL","1","NULL","72ae2cb6b55a581da7c0284354ac68fdd69e8e3d","2014-10-30 12:26:14","NULL","NULL","NULL","NULL","NULL"
"1345834","z_ww1986@163.com","NULL","NULL","2","NULL","","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","1","2015-05-07 11:05:13","2015-05-07 11:05:13","NULL","0","NULL","0","NULL","f4245ae16bff291622e0018f3aa6b78390434bba","NULL","NULL","NULL","NULL","NULL","NULL"
"1237308","z_xinyao@126.com","NULL","NULL","2","NULL","","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","1","2014-09-09 22:45:18","2014-09-09 22:45:18","NULL","0","NULL","0","NULL","fcbefd365ebe8a055deddcd0afea9d56aa8609e5","NULL","NULL","NULL","NULL","NULL","NULL"
"1242432","z_x_shou@qq.com","NULL","NULL","2","NULL","","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","1","2014-09-22 15:12:03","2014-09-22 15:12:03","NULL","0","NULL","0","NULL","901dba3cb3e796a64935ddf2f6e0aff2b90eafcf","NULL","NULL","NULL","NULL","NULL","NULL"
"1343126","z_zh1988@126.com","NULL","NULL","2","NULL","","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","1","2015-05-04 15:37:57","2015-05-04 15:37:57","NULL","0","NULL","0","NULL","927c41f77cac442334ae21b1e289e509fa17a16f","NULL","NULL","NULL","NULL","NULL","NULL"
"1304571","z_z_shanyang@qq.com","NULL","NULL","2","NULL","","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","1","2015-02-19 08:25:53","2015-02-19 08:25:53","NULL","0","NULL","0","NULL","7b02c7af63a61287f1bfc0804f3e6dfe61aa75db","NULL","NULL","NULL","NULL","NULL","NULL"
"1099948","_me@qq.com","NULL","NULL","NULL","NULL","","zlme1009","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","1","2014-01-23 23:25:07","2014-01-23 23:25:07","NULL","0","NULL","0","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL"
"1132079","_stlinshaohe@163.com","fdb587f7e8ff7fcaeabe7c0e56b85a68bb7663db","NULL","1","NULL","afcbe91e92487425b3b368f79bf9ac35","lznet","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","1","2014-04-11 19:22:25","2015-06-02 15:24:04","123.89.81.0","1","NULL","0","NULL","c33ba821f6ebbd46690da2dfd15f5da84eb0e3fa","2015-01-12 20:36:57","NULL","NULL","NULL","NULL","NULL"

EOD
  );

    $ret = getUser($fh);

    $this->assertCount(19, $ret);

    $this->assertEquals( 1132079, $ret['_stlinshaohe@163.com']['id'] ,'"1132079","_stlinshaohe@163.com",');

    $pos = strlen('"id","email","pwd","is_email_confirmed","is_from_wenwen","wenwen_user","token","nick","sex","birthday","tel","is_tel_confirmed","province","city","education","profession","income","hobby","personalDes","identity_num","reward_multiple","register_date","last_login_date","last_login_ip","points","delete_flag","is_info_set","icon_path","uniqkey","token_created_at","origin_flag","created_remote_addr","created_user_agent","campaign_code","password_choice"');


    $this->assertEquals($pos +1 , $ret[" 1160595417@qq.com"]['pointer'] ,'first line point');

    fclose($fh);
  }

  function test_get_max_user_id() 
  {
    $fh = tmpfile();
    fwrite($fh, <<<EOD
id,email,pwd,is_email_confirmed,is_from_wenwen,wenwen_user,token,nick,sex,birthday,tel,is_tel_confirmed,province,city,education,profession,income,hobby,personalDes,identity_num,reward_multiple,register_date,last_login_date,last_login_ip,points,delete_flag,is_info_set,icon_path,uniqkey,token_created_at,origin_flag,created_remote_addr,created_user_agent,campaign_code,password_choice
1291365," 1160595417@qq.com",2ef75e7c46e06b90507e4d47780fd8426857c0ab,,,,,QQ懂你,,,,,,,,,,,,,1,"2015-01-24 10:29:25","2015-01-24 10:29:25",,0,0,1,,,,,,,,
1374309," 1766995961@qq.com",5ffad97ae3afff80a04060a03cde79e5743e687b,,,,,QQ顾小白,,,,,,,,,,,,,1,"2015-07-03 16:43:14","2015-07-03 16:43:14",,1,0,1,,,,,,,,
1378657," 209772454@qq.com",d09930b1d566b15a728eb82bd24402f7b83ecb30,,,,,QQD-Oneight,,,,,,,,,,,,,1,"2015-07-10 11:01:14","2015-07-10 11:01:14",,1,0,1,,,,,,,,
1379445," 2245303447@qq.com",639ce910d44d212f20397b86bfa57717b7a22e2d,,,,,QQ丫丫,2,1994-10,,,9,88,,,103,"1,2,3,4,7,9,11,12",,,1,"2015-07-11 19:10:52","2015-07-11 19:28:07",39.182.89.128,26,0,1,,,,,,,,
1331179," 2292220927@qq.com",643af5f1dccc9301c97b005a927ffda7fbcdc540,,,,,QQ交予我.,,,,,,,,,,,,,1,"2015-04-18 15:30:57","2015-04-18 15:30:57",,0,0,1,,,,,,,,
1304351," 3145737585@qq.com",94f696b7723b22b4dec1b8c2d58a848e236d5059,,,,,QQ小霸王.☀,,,,,,,,,,,,,1,"2015-02-18 18:28:52","2015-02-18 18:28:52",,0,0,1,,,,,,,,
1277673," 3165376163@qq.com",a1f243356e9c1f3670b697491b9e42bf61cb1396,,,,,QQ梨落落,,,,,,,,,,,,,1,"2014-12-15 16:24:18","2014-12-15 16:24:18",,1,1,1,,,,,,,,
1349140," 3226706849@qq.com",dfa89a28e5ecc965981676629308856009036c35,,,,,QQ联友国旅--刘欢,,,,,,,,,,,,,1,"2015-05-14 11:30:12","2015-05-14 11:30:12",,0,0,1,,,,,,,,
1325239," 3244434708@qq.com",e36c7eae682695a81ab3f883e9ab8f11dc3ebda0,,,,,"QQ★보고싶다 진짜~★",,,,,,,,,,,,,1,"2015-04-05 13:43:27","2015-04-05 13:43:27",,0,0,1,,,,,,,,
1378015,z_ch_hui@126.com,,,2,,,,,,,,,,,,,,,,1,"2015-07-09 10:42:54","2015-07-09 10:42:54",,0,,0,,1ff490bcd868afc46523a7bba3aca21173173525,,,,,,
1050778,z_j1224@163.com,b2c7101f2856b9692362716467c87bca2cef9c21,,1,,,缘字诀,1,1988-5,13585926135,,1,1,,,103,"1,2,3",,,1,"2013-08-06 17:24:05","2014-03-01 10:49:15",116.247.87.130,0,,1,,,,,,,,
1257919,z_q_nancy@163.com,1b9ed0d64ac9e60283d0b30b8760247267d34210,,2,,0e1eef00d07f86563dfcc9b50c0e6cb2,prettyzq,2,1986-8,13526810713,,14,164,,,102,"1,2,3,4,5,6,7,8,9,10,11,12",,,1,"2014-10-28 12:08:31","2014-10-30 12:26:17",49.122.70.12,0,,1,,72ae2cb6b55a581da7c0284354ac68fdd69e8e3d,"2014-10-30 12:26:14",,,,,
1345834,z_ww1986@163.com,,,2,,,,,,,,,,,,,,,,1,"2015-05-07 11:05:13","2015-05-07 11:05:13",,0,,0,,f4245ae16bff291622e0018f3aa6b78390434bba,,,,,,
1237308,z_xinyao@126.com,,,2,,,,,,,,,,,,,,,,1,"2014-09-09 22:45:18","2014-09-09 22:45:18",,0,,0,,fcbefd365ebe8a055deddcd0afea9d56aa8609e5,,,,,,
1242432,z_x_shou@qq.com,,,2,,,,,,,,,,,,,,,,1,"2014-09-22 15:12:03","2014-09-22 15:12:03",,0,,0,,901dba3cb3e796a64935ddf2f6e0aff2b90eafcf,,,,,,
1343126,z_zh1988@126.com,,,2,,,,,,,,,,,,,,,,1,"2015-05-04 15:37:57","2015-05-04 15:37:57",,0,,0,,927c41f77cac442334ae21b1e289e509fa17a16f,,,,,,
1304571,z_z_shanyang@qq.com,,,2,,,,,,,,,,,,,,,,1,"2015-02-19 08:25:53","2015-02-19 08:25:53",,0,,0,,7b02c7af63a61287f1bfc0804f3e6dfe61aa75db,,,,,,
1099948,_me@qq.com,,,,,,zlme1009,,,,,,,,,,,,,1,"2014-01-23 23:25:07","2014-01-23 23:25:07",,0,,0,,,,,,,,
1132079,_stlinshaohe@163.com,fdb587f7e8ff7fcaeabe7c0e56b85a68bb7663db,,1,,afcbe91e92487425b3b368f79bf9ac35,lznet,,,,,,,,,,,,,1,"2014-04-11 19:22:25","2015-06-02 15:24:04",123.89.81.0,0,,0,,c33ba821f6ebbd46690da2dfd15f5da84eb0e3fa,"2015-01-12 20:36:57",,,,,
EOD

  );

    $id = get_max_user_id( $fh);


    $this->assertEquals( '1379445' ,$id, 'the id in last line is max id');
    fclose($fh);
  }


  function test_generate_user_data_both_exsit_debug_slash()
  {
     // ERROR 1292 (22007) at line 877: Incorrect datetime value: '0' for column 'register_date' at row 120173
  $this->before_test();
  $expected_user_csv_file ='/data/91jili/merge/export/test.migrate_user.csv'; 
  @exec('rm -rf '.$expected_user_csv_file);

// profile 
  global $panelist_profile_file_handle ;
  $panelist_profile_file_handle = fopen('php://memory','r+');
  fwrite($panelist_profile_file_handle,<<<EOD
id,panelist_id,nickname,show_sex,show_birthday,biography,hobby,fav_music,monthly_wish,website_url,updated_at,created_at
72212,638663,nic,1,1,"/\```/\",,mj,,,"2011-10-12 16:56:15","2011-10-12 16:56:15"
EOD
);
  global $panelist_profile_indexs;
  $panelist_profile_indexs= build_file_index($panelist_profile_file_handle, 'panelist_id');

// detail 
  global $panelist_detail_file_handle ;
  $panelist_detail_file_handle = fopen('php://memory','r+');
  fwrite($panelist_detail_file_handle,<<<EOD
panelist_id,name_first,name_middle,name_last,furigana_first,furigana_middle,furigana_last,age,zip1,zip2,address1,address2,address3,home_type_code,home_year,tel1,tel2,tel3,tel_mobile1,tel_mobile2,tel_mobile3,mobile_number,marriage_code,child_code,child_num,income_family_code,income_personal_code,job_code,industry_code,work_section_code,graduation_code,industry_code_family,internet_starttime_code,internet_usetime_code,last_answer_date,updated_at,created_at
638663,何绮华,,,,,,,,,,,,,,,,,,,,,,,,,3,11,99,99,3,,,,"2014-03-09 20:37:50","2014-03-09 20:37:50","2011-07-10 01:44:21"
EOD
);

  global $panelist_detail_indexs;
  $panelist_detail_indexs= build_file_index($panelist_detail_file_handle, 'panelist_id');

  // image
      global $panelist_profile_image_file_handle ;
      $panelist_profile_image_file_handle = fopen('php://memory','r+');
      fwrite($panelist_profile_image_file_handle,<<<EOD
panelist_id,hash,s_file,s_width,s_height,m_file,m_width,m_height,l_file,l_width,l_height,delete_flag,updated_at,created_at
638663,e26363e3cac065170b663c2233e93b44866930ea,e/2/6/e26363e3cac065170b663c2233e93b44866930ea_s.jpg,30,30,e/2/6/e26363e3cac065170b663c2233e93b44866930ea_m.jpg,90,90,e/2/6/e26363e3cac065170b663c2233e93b44866930ea_l.jpg,270,270,0,"2011-07-10 01:57:13","2011-07-10 01:57:13"
EOD
);
    global $panelist_image_indexs;
    $panelist_image_indexs = build_key_value_index($panelist_profile_image_file_handle, 'panelist_id', 'hash');

  // mobile 
      global $panelist_mobile_number_file_handle ;
      $panelist_mobile_number_file_handle = fopen('php://memory','r+');
      fwrite($panelist_mobile_number_file_handle,<<<EOD
"panelist_id","mobile_number","status_flag","updated_at","created_at"
638663,13660036338,1,"2013-10-31 22:54:55","2013-10-31 22:54:55"
EOD
);

    global $panelist_mobile_indexs;
    $panelist_mobile_indexs = build_key_value_index($panelist_mobile_number_file_handle, 'panelist_id', 'mobile_number');

  $panelist_row = str_getcsv(<<<EOD
638663,2199,2,393141702@qq.com,,ATZ1h54q5B8=,blowfish,★★★★★アジア事業戦略室★★★★★,"2013-12-09 10:57:46","2011-07-10 01:44:21",113.111.122.122,"Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; Trident/4.0; .NET CLR 2.0.50727; .NET CLR 3.0.450",1,2,1992-11-30,2,Google_01,"2014-07-17 18:46:50"
EOD
);
  
  $user_row = str_getcsv(<<<EOD
1119889,393141702@qq.com,8ef4e8d43e373ea6e8df90d492c343819ec9e82e,,1,,,nic0,2,1992-11,13660036338,,17,209,,,100,"1,2,3,5,6,7,9,10",,,1,"2014-03-09 19:39:34","2014-03-12 18:06:33",59.41.205.64,0,,1,,5ebbf5283c73a5eea90416e4ea95f75bd74a04ec,,,,,,
EOD
) ;
  generate_user_data_both_exsit($panelist_row, $user_row);

  $expected = <<<EOD
1119889,393141702@qq.com,8ef4e8d43e373ea6e8df90d492c343819ec9e82e,1,1,NULL,,nic,2,1992-11-30,13660036338,1,17,209,3,11,102,"1,2,3,5,6,7,9,10","/\\\\\\\\```/\\\\\\\\",,1,"2011-07-10 00:44:21","2014-07-17 17:46:50",59.41.205.64,0,0,1,e26363e3cac065170b663c2233e93b44866930ea,5ebbf5283c73a5eea90416e4ea95f75bd74a04ec,NULL,3,113.111.122.122,"Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; Trident/4.0; .NET CLR 2.0.50727; .NET CLR 3.0.450",Google_01,1,mj,,99,99

EOD;
    $this->assertFileExists($expected_user_csv_file); 

    $this->assertEquals($expected, file_get_contents($expected_user_csv_file));

    $this->after_test();

  }
  function test_generate_user_data_both_exsit_debug_province_null()
  {
    $this->before_test();

    $expected_user_csv_file ='/data/91jili/merge/export/test.migrate_user.csv'; 

    @exec('rm -rf '.$expected_user_csv_file);


    $panelist_row = str_getcsv(<<<EOD
440058,,2,1326671454@qq.com,,zRKJGjLNww4=,blowfish,★★★★★アジア事業戦略室★★★★★,"2011-05-10 07:00:45","2010-10-26 14:12:08",112.115.30.128,"Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; 360SE)",1,2,0000-00-00,2,Google_01,"2010-10-26 14:12:09"
EOD
);
    $user_row = str_getcsv(<<<EOD
1056325,1326671454@qq.com,4109120729f5a146b35d920875ba5cdbe704a696,,1,,,天使之恋,,,,,,,,,,,,,1,"2013-08-14 16:52:34","2013-08-14 16:52:34",,0,,0,,,,,,,,
EOD
) ;
     generate_user_data_both_exsit($panelist_row, $user_row);

     $this->assertFileExists($expected_user_csv_file); 
     $return = str_getcsv(file_get_contents($expected_user_csv_file));
     $this->assertEquals('NULL', $return[12], 'province');

    $this->after_test();
  }

  function test_generate_user_data_both_exsit_debug_education_null()
  {
    $this->before_test();

    $expected_user_csv_file ='/data/91jili/merge/export/test.migrate_user.csv'; 

    @exec('rm -rf '.$expected_user_csv_file);
  global $panelist_detail_file_handle ;
  $panelist_detail_file_handle = fopen('php://memory','r+');
  fwrite($panelist_detail_file_handle,<<<EOD
panelist_id,name_first,name_middle,name_last,furigana_first,furigana_middle,furigana_last,age,zip1,zip2,address1,address2,address3,home_type_code,home_year,tel1,tel2,tel3,tel_mobile1,tel_mobile2,tel_mobile3,mobile_number,marriage_code,child_code,child_num,income_family_code,income_personal_code,job_code,industry_code,work_section_code,graduation_code,industry_code_family,internet_starttime_code,internet_usetime_code,last_answer_date,updated_at,created_at

EOD
);

  global $panelist_detail_indexs;
  $panelist_detail_indexs = build_file_index($panelist_detail_file_handle, 'panelist_id');

    $panelist_row = str_getcsv(<<<EOD
226761,2355,2,sdxyh3616@sina.com,,cb8de994c51efb6998b549d6e0c3497f,md5_plain,,"2010-05-31 03:11:24","2010-05-31 03:11:24",,,1,0,0000-00-00,1,manmanzou_201005,
EOD
);
    $user_row = str_getcsv(<<<EOD
1052922,sdxyh3616@sina.com,6b25ec90e2ad59d8f5046840de21ea348154363f,,1,,,bete2000,,,,,,,,,,,,,1,"2013-08-10 21:04:17","2013-08-10 21:04:17",,0,,0,,,,,,,,
EOD
) ;
     generate_user_data_both_exsit($panelist_row, $user_row);
     $expected_user_csv_file ='/data/91jili/merge/export/test.migrate_user.csv'; 

     $this->assertFileExists($expected_user_csv_file); 
     $return = str_getcsv(file_get_contents($expected_user_csv_file));
     $this->assertEquals('NULL', $return[14], 'education');

    $this->after_test();
  }

  function test_generate_user_data_both_exsit_debug()
  {
    $this->before_test();

    $expected_user_csv_file ='/data/91jili/merge/export/test.migrate_user.csv'; 

    @exec('rm -rf '.$expected_user_csv_file);
    $panelist_row = str_getcsv(<<<EOD
19,2002,2,qianqizhen@sina.com,,DIqpJ2jiaHM=,blowfish,76acb8b7f6d767bdf6955c02f0a7c128,"2011-02-25 19:42:21","2009-10-30 10:44:21",116.228.205.38,"Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0; .NET CLR 2.0.50727; InfoPath.2; .NET",1,1,1981-08-04,2,,"2013-12-19 17:48:55"
EOD
);
    $user_row = str_getcsv(<<<EOD
118,qianqizhen@sina.com,38a124223e4c09ed42b9a16b320a3dbbb29b4776,,,,,Qianqizhen,,,,,,,,,,,,,1,"2013-06-14 14:48:28","2013-06-14 14:48:28",,0,,0,,,,,,,,
EOD
) ;
     generate_user_data_both_exsit($panelist_row, $user_row);
     $expected_user_csv_file ='/data/91jili/merge/export/test.migrate_user.csv'; 

     $this->assertFileExists($expected_user_csv_file); 
     $return = str_getcsv(file_get_contents($expected_user_csv_file));
     $this->assertEquals('NULL', $return[4], 'keep jili is_from_wenwen unchanged ');
     $this->assertEquals('NULL', $return[5], 'keep jili wenwen_user unchanged ');
     $this->assertEquals('NULL', $return[11], 'use ww mobile always confirmed');
     $this->assertEquals('NULL', $return[29], 'token_created_at');

    $this->after_test();
  }

  function test_generate_user_data_both_exsit()
  {
    $this->before_test();

    $expected_user_csv_file ='/data/91jili/merge/export/test.migrate_user.csv'; 

    @exec('rm -rf '.$expected_user_csv_file);

//"id","panel_region_id","panel_id","email","login_id","login_password","login_password_crypt_type","login_password_salt","updated_at","created_at","created_remote_addr","created_user_agent","login_valid_flag","sex_code","birthday","panelist_status","campaign_code","last_login_time"
    $panelist_row = str_getcsv(<<<EOD
"6","2000","2","tao.jiang@d8aspring.com","NULL","DIqpJ2jiaHM=","blowfish","76acb8b7f6d767bdf6955c02f0a7c128","2011-02-25 19:42:21","2009-10-30 10:44:21","116.228.205.38","Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0; .NET CLR 2.0.50727; InfoPath.2; .NET","1","1","1981-08-04","2","offer99","2013-12-19 17:48:55"

EOD
);

//"id","email","pwd","is_email_confirmed","is_from_wenwen","wenwen_user","token","nick","sex","birthday","tel","is_tel_confirmed","province","city","education","profession","income","hobby","personalDes","identity_num","reward_multiple","register_date","last_login_date","last_login_ip","points","delete_flag","is_info_set","icon_path","uniqkey","token_created_at","origin_flag","created_remote_addr","created_user_agent","campaign_code","password_choice"
    $user_row = str_getcsv(<<<EOD
"1291365","tao_jiang@voyagegroup.com","2ef75e7c46e06b90507e4d47780fd8426857c0ab","","1","tao.jiang@d8aspring.com","","QQ懂你","2","1988-1","13732634246","NULL","3","18","NULL","NULL","101","1,9,11","NULL","NULL","1","2015-01-24 10:29:25","2015-01-24 10:29:25","11.22.33.44","77","0","1","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL"
EOD
) ;

     generate_user_data_both_exsit($panelist_row, $user_row);
     $expected_user_csv_file ='/data/91jili/merge/export/test.migrate_user.csv'; 
     $this->assertFileExists($expected_user_csv_file); 
     $return = str_getcsv(file_get_contents($expected_user_csv_file));
     $this->assertCount(39, $return, 'merged user array has 39 items');
     $this->assertEquals(1291365, $return[0], 'same as jili.user.id 1291366');
     $this->assertEquals('tao.jiang@d8aspring.com', $return[1], 'use ww email ');
     $this->assertEquals('2ef75e7c46e06b90507e4d47780fd8426857c0ab', $return[2], 'use ww pass, just keep the jili"pass unchanged ');

     $this->assertEquals('1', $return[3], 'ww email is confirmedis_email_confirmed ');
     $this->assertEquals('1', $return[4], 'keep jili is_from_wenwen unchanged ');
     $this->assertEquals('tao.jiang@d8aspring.com', $return[5], 'keep jili wenwen_user unchanged ');
     $this->assertEquals('', $return[6], 'keep jili token unchanged ');
     $this->assertEquals('琪琪琪', $return[7], 'use ww profile.nickname');
     $this->assertEquals(1, $return[8], 'use ww sex code');
     $this->assertEquals('1981-08-04', $return[9], 'use jili birthday ');
     $this->assertEquals('13052550759', $return[10], 'use ww mobile ');
     $this->assertEquals('1', $return[11], 'use ww mobile always confirmed');
     $this->assertEquals('1', $return[12], 'province, 1 in region_mapping ');
     $this->assertEquals('2', $return[13], 'city, 2 in region_mapping');
     $this->assertEquals('3', $return[14], 'education, 3 is from ww detail.graduation_code');
     $this->assertEquals('4',$return[15], 'profession, 18 is from ww detail.job_cdoe');
     $this->assertEquals('119', $return[16], 'income, 3 is from ww detail.income_personal_code');
     $this->assertEquals('1,9,11', $return[17], 'hobby, profile.hobby ');
     $this->assertEquals('出生:毕业:工作:经历:', $return[18], 'personalDes, profile.biography ');
     $this->assertEquals('NULL', $return[19], 'identity_num, ww NULL ');
     $this->assertEquals('1', $return[20], 'reward_multiple, always 1');
     $this->assertEquals('2009-10-30 09:44:21', $return[21], 'created_at, 1 hour after ');
     $this->assertEquals('2013-12-19 16:48:55', $return[22], 'last login date, last login time');
     $this->assertEquals('11.22.33.44', $return[23], 'last login ip, use jili if exists');
     $this->assertEquals('88', $return[24], 'sum');
     $this->assertEquals('0', $return[25], 'delete_flag, ww always');
     $this->assertEquals('1', $return[26], 'is_info_set, ww always');
     $this->assertEquals('c05fc2fdb476d327e418b9950ba89c32c443394c', $return[27], 'icon_path');
     $this->assertEquals('NULL', $return[28], 'uniqkey');
     $this->assertEquals('NULL', $return[29], 'token_created_at');
     $this->assertEquals('3', $return[30], 'origin_flag');
     $this->assertEquals('116.228.205.38', $return[31], 'created_remote_addr');
     $this->assertEquals('Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0; .NET CLR 2.0.50727; InfoPath.2; .NET', $return[32], 'created_user_agent');
     $this->assertEquals('offer99', $return[33], 'campaign_code');
     $this->assertEquals('1', $return[34], 'password_choice, 1:ww');
     $this->assertEquals('都一般', $return[35], 'fav_music, ww profile');
     $this->assertEquals('要不中个500万玩玩？', $return[36], 'monthly_wish, profile');
     $this->assertEquals('3', $return[37], 'industry_code, detail ');
     $this->assertEquals('9', $return[38], 'work_section_code, detail');




     $this->after_test();
  }

  function test_generate_user_data_both_exsit_is_from_wenwen_2()
  {
    $this->before_test();
    @exec('rm -rf '.$expected_user_csv_file);
    $panelist_row = str_getcsv(<<<EOD
"6","2000","2","tao.jiang@d8aspring.com","NULL","DIqpJ2jiaHM=","blowfish","76acb8b7f6d767bdf6955c02f0a7c128","2011-02-25 19:42:21","2009-10-30 10:44:21","116.228.205.38","Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0; .NET CLR 2.0.50727; InfoPath.2; .NET","1","1","1981-08-04","2","offer99","2013-12-19 17:48:55"

EOD
) ;
    $user_row = str_getcsv(<<<EOD
"1291365","tao_jiang@voyagegroup.com","2ef75e7c46e06b90507e4d47780fd8426857c0ab","","2","NULL","","QQ懂你","2","1988-1","13052550759","NULL","3","18","NULL","NULL","NULL","1,9,11","NULL","NULL","1","2015-01-24 10:29:25","2015-01-24 10:29:25","11.22.33.44","77","0","1","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL"
EOD
) ;
     generate_user_data_both_exsit($panelist_row, $user_row);
     $expected_user_csv_file ='/data/91jili/merge/export/test.migrate_user.csv'; 
     $this->assertFileExists($expected_user_csv_file); 
     $return = str_getcsv(file_get_contents($expected_user_csv_file));
     $this->assertEquals('2', $return[4], 'always 3 is merged with  wenwen');
    $this->after_test();
  }

  function test_generate_user_data_both_exsit_is_from_wenwen_null()
  {
    //
    $this->before_test();
    @exec('rm -rf '.$expected_user_csv_file);
    $panelist_row = str_getcsv(<<<EOD
"6","2000","2","tao.jiang@d8aspring.com","NULL","DIqpJ2jiaHM=","blowfish","76acb8b7f6d767bdf6955c02f0a7c128","2011-02-25 19:42:21","2009-10-30 10:44:21","116.228.205.38","Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0; .NET CLR 2.0.50727; InfoPath.2; .NET","1","1","1981-08-04","2","offer99","2013-12-19 17:48:55"

EOD
) ;
    $user_row = str_getcsv(<<<EOD
"1291365","tao_jiang@voyagegroup.com","2ef75e7c46e06b90507e4d47780fd8426857c0ab","","NULL","NULL","","QQ懂你","2","1988-1","13052550759","NULL","3","18","NULL","NULL","NULL","1,9,11","NULL","NULL","1","2015-01-24 10:29:25","2015-01-24 10:29:25","11.22.33.44","77","0","1","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL"
EOD
) ;
     generate_user_data_both_exsit($panelist_row, $user_row);
     $expected_user_csv_file ='/data/91jili/merge/export/test.migrate_user.csv'; 
     $this->assertFileExists($expected_user_csv_file); 
     $return = str_getcsv(file_get_contents($expected_user_csv_file));
     $this->assertEquals('NULL', $return[4], 'always 3 is merged with  wenwen');
     $this->after_test();
  }


  function test_generate_user_data_both_exsit_ww_mobile_null()
  {
    $this->before_test();
    $expected_user_csv_file ='/data/91jili/merge/export/test.migrate_user.csv'; 
    @exec('rm -rf '.$expected_user_csv_file);

    $panelist_row = str_getcsv(<<<EOD
"19","2000","2","tao.jiang@d8aspring.com","NULL","DIqpJ2jiaHM=","blowfish","76acb8b7f6d767bdf6955c02f0a7c128","2011-02-25 19:42:21","2009-10-30 10:44:21","116.228.205.38","Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0; .NET CLR 2.0.50727; InfoPath.2; .NET","1","1","1981-08-04","2","offer99","2013-12-19 17:48:55"

EOD
) ;
    $user_row = str_getcsv(<<<EOD
"1291365","tao_jiang@voyagegroup.com","2ef75e7c46e06b90507e4d47780fd8426857c0ab","","1","tao.jiang@d8aspring.com","","QQ懂你","2","1988-1","13732634246","NULL","3","18","NULL","NULL","NULL","1,9,11","NULL","NULL","1","2015-01-24 10:29:25","2015-01-24 10:29:25","11.22.33.44","77","0","1","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL"
EOD
) ;

     generate_user_data_both_exsit($panelist_row, $user_row);
     $expected_user_csv_file ='/data/91jili/merge/export/test.migrate_user.csv'; 
     $this->assertFileExists($expected_user_csv_file); 
     $return = str_getcsv(file_get_contents($expected_user_csv_file));

     $this->assertEquals('13732634246', $return[10], 'no ww mobile, use jili.user.tel');
     $this->assertEquals('NULL', $return[11], 'ww mobile no exits,use jili.user.is_tel_confirmed');

     $this->after_test();

  }


  function test_generate_user_data_both_exsit_all_mobile_null()
  {
    $this->before_test();
    $expected_user_csv_file ='/data/91jili/merge/export/test.migrate_user.csv'; 
    @exec('rm -rf '.$expected_user_csv_file);

    $panelist_row = str_getcsv(<<<EOD
"19","2000","2","tao.jiang@d8aspring.com","NULL","DIqpJ2jiaHM=","blowfish","76acb8b7f6d767bdf6955c02f0a7c128","2011-02-25 19:42:21","2009-10-30 10:44:21","116.228.205.38","Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0; .NET CLR 2.0.50727; InfoPath.2; .NET","1","1","1981-08-04","2","offer99","2013-12-19 17:48:55"

EOD
) ;
    $user_row = str_getcsv(<<<EOD
"1291365","tao_jiang@voyagegroup.com","2ef75e7c46e06b90507e4d47780fd8426857c0ab","","1","tao.jiang@d8aspring.com","","QQ懂你","2","1988-1","","NULL","3","18","NULL","NULL","NULL","1,9,11","NULL","NULL","1","2015-01-24 10:29:25","2015-01-24 10:29:25","11.22.33.44","77","0","1","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL"
EOD
) ;

     generate_user_data_both_exsit($panelist_row, $user_row);
     $expected_user_csv_file ='/data/91jili/merge/export/test.migrate_user.csv'; 
     $this->assertFileExists($expected_user_csv_file); 
     $return = str_getcsv(file_get_contents($expected_user_csv_file));

     $this->assertEquals('', $return[10], 'no ww mobile , no jili mobile ');
     $this->assertEquals('NULL', $return[11], 'ww& jili  mobile no exits, no confirmed');

     $this->after_test();

  }

  function test_generate_user_data_both_exsit_ww_region_null()
  {
    $this->before_test();
    $expected_user_csv_file ='/data/91jili/merge/export/test.migrate_user.csv'; 
    @exec('rm -rf '.$expected_user_csv_file);

    $panelist_row = str_getcsv(<<<EOD
"19","NULL","2","tao.jiang@d8aspring.com","NULL","DIqpJ2jiaHM=","blowfish","76acb8b7f6d767bdf6955c02f0a7c128","2011-02-25 19:42:21","2009-10-30 10:44:21","116.228.205.38","Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0; .NET CLR 2.0.50727; InfoPath.2; .NET","1","1","1981-08-04","2","offer99","2013-12-19 17:48:55"

EOD
) ;
    $user_row = str_getcsv(<<<EOD
"1291365","tao_jiang@voyagegroup.com","2ef75e7c46e06b90507e4d47780fd8426857c0ab","","1","tao.jiang@d8aspring.com","","QQ懂你","2","1988-1","","NULL","5","19","NULL","NULL","NULL","1,9,11","NULL","NULL","1","2015-01-24 10:29:25","2015-01-24 10:29:25","11.22.33.44","77","0","1","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL"
EOD
) ;

     generate_user_data_both_exsit($panelist_row, $user_row);
     $expected_user_csv_file ='/data/91jili/merge/export/test.migrate_user.csv'; 
     $this->assertFileExists($expected_user_csv_file); 
     $return = str_getcsv(file_get_contents($expected_user_csv_file));

     $this->assertEquals('5', $return[12], 'province, 3 is from jili.user');
     $this->assertEquals('19', $return[13], 'city, 18 is from jili.user');


     $this->after_test();

  }

function test_generate_user_data_both_exsit_all_region_null()
  {
    $this->before_test();
    $expected_user_csv_file ='/data/91jili/merge/export/test.migrate_user.csv'; 
    @exec('rm -rf '.$expected_user_csv_file);

    $panelist_row = str_getcsv(<<<EOD
"19","NULL","2","tao.jiang@d8aspring.com","NULL","DIqpJ2jiaHM=","blowfish","76acb8b7f6d767bdf6955c02f0a7c128","2011-02-25 19:42:21","2009-10-30 10:44:21","116.228.205.38","Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0; .NET CLR 2.0.50727; InfoPath.2; .NET","1","1","1981-08-04","2","offer99","2013-12-19 17:48:55"

EOD
) ;
    $user_row = str_getcsv(<<<EOD
"1291365","tao_jiang@voyagegroup.com","2ef75e7c46e06b90507e4d47780fd8426857c0ab","","1","tao.jiang@d8aspring.com","","QQ懂你","2","1988-1","","NULL","NULL","NULL","NULL","NULL","NULL","1,9,11","NULL","NULL","1","2015-01-24 10:29:25","2015-01-24 10:29:25","11.22.33.44","77","0","1","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL"
EOD
) ;

     generate_user_data_both_exsit($panelist_row, $user_row);
     $expected_user_csv_file ='/data/91jili/merge/export/test.migrate_user.csv'; 
     $this->assertFileExists($expected_user_csv_file); 
     $return = str_getcsv(file_get_contents($expected_user_csv_file));

     $this->assertEquals('NULL', $return[12], 'province, 3 is from jili.user');
     $this->assertEquals('NULL', $return[13], 'city, 18 is from jili.user');

     $this->after_test();

  }


function test_generate_user_data_both_exsit_ww_detail_null()
{
    $this->before_test();
    $expected_user_csv_file ='/data/91jili/merge/export/test.migrate_user.csv'; 
    @exec('rm -rf '.$expected_user_csv_file);

    $panelist_row = str_getcsv(<<<EOD
"19","NULL","2","tao.jiang@d8aspring.com","NULL","DIqpJ2jiaHM=","blowfish","76acb8b7f6d767bdf6955c02f0a7c128","2011-02-25 19:42:21","2009-10-30 10:44:21","116.228.205.38","Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0; .NET CLR 2.0.50727; InfoPath.2; .NET","1","1","1981-08-04","2","offer99","2013-12-19 17:48:55"

EOD
) ;
    $user_row = str_getcsv(<<<EOD
"1291365","tao_jiang@voyagegroup.com","2ef75e7c46e06b90507e4d47780fd8426857c0ab","","1","tao.jiang@d8aspring.com","","QQ懂你","2","1988-1","","NULL","NULL","NULL","NULL","NULL","101","1,9,11","NULL","NULL","1","2015-01-24 10:29:25","2015-01-24 10:29:25","11.22.33.44","77","0","1","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL"
EOD
) ;

     generate_user_data_both_exsit($panelist_row, $user_row);
     $expected_user_csv_file ='/data/91jili/merge/export/test.migrate_user.csv'; 
     $this->assertFileExists($expected_user_csv_file); 
     $return = str_getcsv(file_get_contents($expected_user_csv_file));

     $this->assertEquals('NULL', $return[14], 'education, no ww detail  ');
     $this->assertEquals('NULL',$return[15], 'profession,no ww detail   ');
     $this->assertEquals('101', $return[16], 'income, no ww detail , use jili.income');

     $this->after_test();

  }


  function test_generate_user_data_only_wenwen()
  {
    $this->before_test();
      $expected_user_csv_file ='/data/91jili/merge/export/test.migrate_user.csv'; 
      @exec('rm -rf '.$expected_user_csv_file);

    $panelist_row = str_getcsv(<<<EOD
"6","2355","2","tao.jiang@d8aspring.com","NULL","DIqpJ2jiaHM=","blowfish","76acb8b7f6d767bdf6955c02f0a7c128","2011-02-25 19:42:21","2009-10-30 00:44:21","116.228.205.38","Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0; .NET CLR 2.0.50727; InfoPath.2; .NET","1","1","1981-08-04","2","offer99","2013-12-20 00:48:55"

EOD
) ;

     generate_user_data_only_wenwen($panelist_row, 1);
     $this->assertFileExists($expected_user_csv_file); 
     $return = str_getcsv(file_get_contents($expected_user_csv_file));

     $this->assertCount(39, $return, 'merged user array has 39 items');
     $this->assertEquals(1, $return[0], 'user id is 1');
     $this->assertEquals('tao.jiang@d8aspring.com', $return[1], 'use ww email ');
     $this->assertEquals('', $return[2], 'pwd is empty for ww only');

     $this->assertEquals('1', $return[3], 'empty is_email_confirmed ');
     $this->assertEquals('3', $return[4], 'always 3 is merged with  wenwen is_from_wenwen');
     $this->assertEquals('NULL', $return[5], 'set wenwe_user to NULL');
     $this->assertEquals('', $return[6], 'set  token ""');
     $this->assertEquals("琪琪琪", $return[7], 'use ww profile.nickname');
     $this->assertEquals(1, $return[8], 'use ww sex code');
#     $this->assertEquals('1981-8', $return[9], 'use jili birthday  ToDEbug:');
     $this->assertEquals('13052550759', $return[10], 'use ww mobile ');
     $this->assertEquals('1', $return[11], 'use ww mobile always confirmed');

     $this->assertEquals('32', $return[12], 'province, 1 in region_mapping ');
     $this->assertEquals('363', $return[13], 'city, 2 in region_mapping');
     $this->assertEquals('3', $return[14], 'education, 3 is from ww detail.graduation_code');
     $this->assertEquals('4',$return[15], 'profession, 18 is from ww detail.job_cdoe');
     $this->assertEquals('119', $return[16], 'income, 3 is from ww detail.income_personal_code');

     $this->assertEquals('NULL', $return[17], 'hobby, profile.hobby ToDebug:');
     $this->assertEquals('出生:毕业:工作:经历:', $return[18], 'personalDes, profile.biography ');
     $this->assertEquals('NULL', $return[19], 'identity_num, ww NULL ');
     $this->assertEquals('1', $return[20], 'reward_multiple, always 1');
     $this->assertEquals('2009-10-29 23:44:21', $return[21], 'created_at, 1 hour after, day changed');
     $this->assertEquals('2013-12-19 23:48:55', $return[22], 'last login date, last login time');
     $this->assertEquals('NULL', $return[23], 'last login ip , use jili if exists');
     $this->assertEquals('11', $return[24], 'sum');
     $this->assertEquals('0', $return[25], 'delete_flag, ww always');
     $this->assertEquals('1', $return[26], 'is_info_set, ww always');
     $this->assertEquals('c05fc2fdb476d327e418b9950ba89c32c443394c', $return[27], 'icon_path');
     $this->assertEquals('NULL', $return[28], 'uniqkey');
     $this->assertEquals('NULL', $return[29], 'token_created_at');
     $this->assertEquals('2', $return[30], 'origin_flag');

     $this->assertEquals('116.228.205.38', $return[31], 'created_remote_addr');
     $this->assertEquals('Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0; .NET CLR 2.0.50727; InfoPath.2; .NET', $return[32], 'created_user_agent');
     $this->assertEquals('offer99', $return[33], 'campaign_code');
     $this->assertEquals('1', $return[34], 'password_choice');
     $this->assertEquals('都一般', $return[35], 'fav_music, ww profile');
     $this->assertEquals('要不中个500万玩玩？', $return[36], 'monthly_wish, profile');
     $this->assertEquals('3', $return[37], 'industry_code, detail ');
     $this->assertEquals('9', $return[38], 'work_section_code, detail');
     $this->after_test();
  }

  function test_generate_user_data_only_wenwen_debug()
  {
  $this->before_test();
  $expected_user_csv_file ='/data/91jili/merge/export/test.migrate_user.csv'; 
  @exec('rm -rf '.$expected_user_csv_file);

/*
45168,guxiansiu@126.com,,1,3,NULL,,NULL,0,0000-00-00,NULL,NULL,32,363,,,,NULL,NULL,NULL,1,"2009-11-10 19:38:36",NULL,NULL,0,0,1,NULL,NULL,NULL,2,,,manmanzou_optout20091110,1,NULL,NULL,,
45168,guxiansiu@126.com,,1,3,NULL,,NULL,0,0000-00-00,NULL,NULL,32,363,NULL,NULL,NULL,NULL,NULL,NULL,1,"2009-11-10 19:38:36",NULL,NULL,0,0,1,NULL,NULL,NULL,2,,,manmanzou_optout20091110,1,NULL,NULL,NULL,

*/

  global $panelist_detail_file_handle ;
  $panelist_detail_file_handle = fopen('php://memory','r+');
  fwrite($panelist_detail_file_handle,<<<EOD
"panelist_id","name_first","name_middle","name_last","furigana_first","furigana_middle","furigana_last","age","zip1","zip2","address1","address2","address3","home_type_code","home_year","tel1","tel2","tel3","tel_mobile1","tel_mobile2","tel_mobile3","mobile_number","marriage_code","child_code","child_num","income_family_code","income_personal_code","job_code","industry_code","work_section_code","graduation_code","industry_code_family","internet_starttime_code","internet_usetime_code","last_answer_date","updated_at","created_at"
410,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,"2009-11-10 20:38:36","2009-11-10 20:38:36"
EOD
);

  global $panelist_detail_indexs;
  $panelist_detail_indexs = build_file_index($panelist_detail_file_handle, 'panelist_id');

  $panelist_row = str_getcsv(<<<EOD
410,2355,2,guxiansiu@126.com,,1d1741f52606db8734952d6cd39a2590,md5_plain,,"2010-05-10 08:08:37","2009-11-10 20:38:36",,,1,0,0000-00-00,2,manmanzou_optout20091110,

EOD
) ;

  generate_user_data_only_wenwen($panelist_row, 1);
  $this->assertFileExists($expected_user_csv_file); 
  $return = str_getcsv(file_get_contents($expected_user_csv_file));
  $this->assertEquals('NULL', $return[14], 'education, NULL is default   detail.graduation_code');
  $this->assertEquals('NULL', $return[15], 'job_code, NULL is default   detail.graduation_code');
  $this->assertEquals('NULL', $return[16], 'income_personal_code, NULL is default   detail.graduation_code');
  $this->assertEquals('NULL', $return[37], 'industry_code, NULL is default   detail.graduation_code');
  $this->assertEquals('NULL', $return[38], 'work_section_code, NULL is default   detail.graduation_code');
  $this->after_test();
  }



  function test_generate_user_data_only_wenwen_debug_register_date_0()
  {
     // ERROR 1292 (22007) at line 877: Incorrect datetime value: '0' for column 'register_date' at row 120173
  $this->before_test();
  $expected_user_csv_file ='/data/91jili/merge/export/test.migrate_user.csv'; 
  @exec('rm -rf '.$expected_user_csv_file);

// profile 
  global $panelist_profile_file_handle ;
  $panelist_profile_file_handle = fopen('php://memory','r+');
  fwrite($panelist_profile_file_handle,<<<EOD
id,panelist_id,nickname,show_sex,show_birthday,biography,hobby,fav_music,monthly_wish,website_url,updated_at,created_at
32596,164778,smader,1,1,"muyou\",,,,,"2011-04-13 19:14:28","2011-04-13 19:14:28"
EOD
);
  global $panelist_profile_indexs;
  $panelist_profile_indexs= build_file_index($panelist_profile_file_handle, 'panelist_id');

// detail 
  global $panelist_detail_file_handle ;
  $panelist_detail_file_handle = fopen('php://memory','r+');
  fwrite($panelist_detail_file_handle,<<<EOD
panelist_id,name_first,name_middle,name_last,furigana_first,furigana_middle,furigana_last,age,zip1,zip2,address1,address2,address3,home_type_code,home_year,tel1,tel2,tel3,tel_mobile1,tel_mobile2,tel_mobile3,mobile_number,marriage_code,child_code,child_num,income_family_code,income_personal_code,job_code,industry_code,work_section_code,graduation_code,industry_code_family,internet_starttime_code,internet_usetime_code,last_answer_date,updated_at,created_at
164778,蒋晓磊,,,,,,,,,,,,,,,,,,,,13782704295,,,,,4,4,3,6,4,,,,"2011-08-20 11:05:02","2011-08-20 11:05:02","2010-04-25 15:44:21"
EOD
);
  global $panelist_detail_indexs;
  $panelist_detail_indexs= build_file_index($panelist_detail_file_handle, 'panelist_id');

  $panelist_row = str_getcsv(<<<EOD
164778,2002,2,897987651@qq.com,,ZPeCGP1n+qKFXicIdA40Ug==,blowfish,664396c4f3d46705872dc562d6ed4a23,"2011-07-30 09:56:28","2010-04-25 15:44:21",10.210.43.82,"Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; Sicent; Mozilla/4.0 (compatible; MSIE 6.0; W",1,1,1986-06-05,2,,"2012-12-22 16:43:41"
EOD

);
 
     generate_user_data_only_wenwen($panelist_row,160129 );
    // is_tel_confirmed 
    if(''=== $row[11] ) {
        $row[11] = 'NULL';
    }
    $expected_user_csv_file ='/data/91jili/merge/export/test.migrate_user.csv'; 
     // 164778,蒋晓磊,,,,,,,,,,,,,,,,,,,,13782704295,,,,,4,4,3,6,4,,,,"2011-08-20 11:05:02","2011-08-20 11:05:02","2010-04-25 15:44:21"
    $expected = <<<EOD
160129,897987651@qq.com,,1,3,NULL,,smader,1,1986-06-05,NULL,NULL,1,1,4,4,103,NULL,"muyou\\\\",NULL,1,"2010-04-25 14:44:21","2012-12-22 15:43:41",NULL,0,0,1,NULL,NULL,NULL,2,10.210.43.82,"Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; Sicent; Mozilla/4.0 (compatible; MSIE 6.0; W",,1,,,3,6

EOD;
    $this->assertFileExists($expected_user_csv_file); 

    $this->assertEquals($expected, file_get_contents($expected_user_csv_file));

    $this->after_test();

  }

  /**
   * @group debug
   *
   **/
  function test_generate_user_data_only_jili_debug_slashes()
  {

      $this->before_test();
      $expected_user_csv_file ='/data/91jili/merge/export/test.migrate_user.csv'; 
      @exec('rm -rf '.$expected_user_csv_file);

      $csv = <<<EOD
1397556,2950546297@qq.com,a78ad7a18c4986259f6b4a704cbfe16768e9cfe5,,,,,"QQ/[.=.]\\",1,1990-8,13729714659,,17,209,,,103,"1,2,8,10",,,1,"2015-08-06 10:38:49","2015-08-06 10:38:58",14.209.198.164,146,0,1,,,,,,,,
EOD;

      $user_row = str_getcsv($csv, ',','"','"');
      generate_user_data_only_jili($user_row);

      $this->assertFileExists($expected_user_csv_file); 
      $return = str_getcsv(file_get_contents($expected_user_csv_file));

      $this->assertEquals('QQ/[.=.]\\\\', $return[7], 'nick escaped' );
      $this->assertEquals('1', $return[20], 'hobby' );
      $this->assertEquals('1,2,8,10', $return[17], 'reward_multiple' );


      $this->after_test();
  }


  function test_generate_user_data_only_jili()
  {

      $this->before_test();
      $expected_user_csv_file ='/data/91jili/merge/export/test.migrate_user.csv'; 
      @exec('rm -rf '.$expected_user_csv_file);

//"id","email","pwd","is_email_confirmed","is_from_wenwen","wenwen_user","token","nick","sex","birthday","tel","is_tel_confirmed","province","city","education","profession","income","hobby","personalDes","identity_num","reward_multiple","register_date","last_login_date","last_login_ip","points","delete_flag","is_info_set","icon_path","uniqkey","token_created_at","origin_flag","created_remote_addr","created_user_agent","campaign_code","password_choice"
      $user_row = str_getcsv(<<<EOD
"1291363","tao_jiang@voyagegroup.com","2ef75e7c46e06b90507e4d47780fd8426857c0ab","","1","NULL","","QQ懂你","2","1988-1","13732634246","NULL","3","18","NULL","NULL","101","1,9,11","简历","132103198010310032","1","2015-01-24 10:29:25","2015-01-24 10:29:25","11.22.33.44","77","0","1","uploads/user/91/1377046582_2187.jpeg","NULL","NULL","NULL","NULL","NULL","NULL","NULL"
EOD
);
      generate_user_data_only_jili($user_row);

      $this->assertFileExists($expected_user_csv_file); 

      $return = str_getcsv(file_get_contents($expected_user_csv_file));

      $this->assertCount(39, $return, 'merged user array has 39 items');
      $this->assertEquals(1291363, $return[0], 'user id is 1');
      $this->assertEquals('tao_jiang@voyagegroup.com', $return[1], 'use ww email ');
      $this->assertEquals('2ef75e7c46e06b90507e4d47780fd8426857c0ab', $return[2], 'password choice');
      $this->assertEquals('NULL', $return[3], 'empty is_email_confirmed ');
      $this->assertEquals('1', $return[4], 'keep 1');
      $this->assertEquals('NULL', $return[5], 'keep original value NULL');
      $this->assertEquals('', $return[6], 'keep original token value NULL');
     $this->assertEquals('QQ懂你', $return[7], 'keep jili.user.nick');
     $this->assertEquals(2, $return[8], 'keep  sex code');
     $this->assertEquals('1988-1', $return[9], 'use jili birthday  ');
     $this->assertEquals('13732634246', $return[10], 'use jili user.tel');

     $this->assertEquals('NULL', $return[11], 'use keep jili user.is_tel_confirmed ');
     $this->assertEquals('3', $return[12], 'use keep jili user.province');
     $this->assertEquals('18', $return[13], 'use keep jili user.city');

     $this->assertEquals('NULL', $return[14], 'education, null in jili ');
     $this->assertEquals('NULL',$return[15], 'profession,null in jili');
     $this->assertEquals('101', $return[16], 'income, keep jili.user.income ');
     $this->assertEquals('1,9,11', $return[17], 'hobby, profile.hobby ToDebug:');
     $this->assertEquals('简历', $return[18], 'personalDes, keep jili');
     $this->assertEquals('132103198010310032', $return[19], 'identity_num, ww NULL ');
     $this->assertEquals('1', $return[20], 'reward_multiple, always 1');
     $this->assertEquals('2015-01-24 10:29:25', $return[21], 'created_at, always 1');
     $this->assertEquals('11.22.33.44', $return[23], 'last login ip');
     $this->assertEquals('77', $return[24], 'sum');


     $this->assertEquals('uploads/user/91/1377046582_2187.jpeg', $return[27], 'icon_path');
     $this->assertEquals('NULL', $return[28], 'uniqkey');
     $this->assertEquals('NULL', $return[29], 'token_created_at');
     $this->assertEquals('1', $return[30], 'origin_flag');
     $this->assertEquals('NULL', $return[31], 'created_remote_addr');
     $this->assertEquals('NULL', $return[32], 'created_user_agent');
     $this->assertEquals('NULL', $return[33], 'campaign_code');
     $this->assertEquals('2', $return[34], 'password_choice');
     $this->assertEquals('NULL', $return[35], 'fav_music');
     $this->assertEquals('NULL', $return[36], 'monthly_wish');
     $this->assertEquals('NULL', $return[37], 'industry_code');
     $this->assertEquals('NULL', $return[38], 'work_section_code');
     $this->assertEquals('0', $return[25], 'delete_flag, ww always');
     $this->assertEquals('1', $return[26], 'is_info_set, ww always');

      $this->after_test();
  }

  function test_generate_user_data_only_jili_is_from_wenwen_null()
  {

      $this->before_test();
      $expected_user_csv_file ='/data/91jili/merge/export/test.migrate_user.csv'; 
      @exec('rm -rf '.$expected_user_csv_file);

      $user_row = str_getcsv(<<<EOD
"1291363","tao_jiang@voyagegroup.com","2ef75e7c46e06b90507e4d47780fd8426857c0ab","","","NULL","","QQ懂你","2","1988-1","13732634246","NULL","3","18","NULL","NULL","101","1,9,11","简历","132103198010310032","1","2015-01-24 10:29:25","2015-01-24 10:29:25","11.22.33.44","77","0","1","uploads/user/91/1377046582_2187.jpeg","NULL","NULL","NULL","NULL","NULL","NULL","NULL"
EOD
);
      generate_user_data_only_jili($user_row);

      $this->assertFileExists($expected_user_csv_file); 

      $return = str_getcsv(file_get_contents($expected_user_csv_file));

      $this->assertEquals('NULL', $return[4], 'empty is_email_confirmed ');

      $this->after_test();
  }

  function test_generate_user_data_only_jili_is_email_set()
  {
      $this->before_test();
// is_email_confirmed not nll
      $user_row = str_getcsv(<<<EOD
"1291363","tao_jiang@voyagegroup.com","2ef75e7c46e06b90507e4d47780fd8426857c0ab","1","1","NULL","","QQ懂你","2","1988-1","13052550759","NULL","3","18","NULL","NULL","NULL","1,9,11","NULL","NULL","1","2015-01-24 10:29:25","2015-01-24 10:29:25","11.22.33.44","77","0","1","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL"
EOD
);
      generate_user_data_only_jili($user_row);

      $expected_user_csv_file ='/data/91jili/merge/export/test.migrate_user.csv'; 
      $this->assertFileExists($expected_user_csv_file); 
      $return = str_getcsv(file_get_contents($expected_user_csv_file));
      $this->assertEquals('1', $return[3], 'is_email_confirmed should not null');

      $this->after_test();
  }

  function test_generate_user_data_only_jili_personalDes_null()
  {
      $this->before_test();
// is_email_confirmed not nll
      $user_row = str_getcsv(<<<EOD
"1291363","tao_jiang@voyagegroup.com","2ef75e7c46e06b90507e4d47780fd8426857c0ab","1","1","NULL","","QQ懂你","2","1988-1","13052550759","NULL","3","18","NULL","NULL","NULL","1,9,11","NULL","NULL","1","2015-01-24 10:29:25","2015-01-24 10:29:25","11.22.33.44","77","0","1","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL"
EOD
);
      generate_user_data_only_jili($user_row);

      $expected_user_csv_file ='/data/91jili/merge/export/test.migrate_user.csv'; 
      $this->assertFileExists($expected_user_csv_file); 
      $return = str_getcsv(file_get_contents($expected_user_csv_file));
      $this->assertEquals('NULL', $return[18], 'personalDes is null');

      $this->after_test();
  }

  function test_generate_user_data_only_jili_identity_num_null()
  {
      $this->before_test();
// is_email_confirmed not nll
      $user_row = str_getcsv(<<<EOD
"1291363","tao_jiang@voyagegroup.com","2ef75e7c46e06b90507e4d47780fd8426857c0ab","1","1","NULL","","QQ懂你","2","1988-1","13052550759","NULL","3","18","NULL","NULL","NULL","1,9,11","NULL","NULL","1","2015-01-24 10:29:25","2015-01-24 10:29:25","11.22.33.44","77","0","1","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL"
EOD
);
      generate_user_data_only_jili($user_row);
      $expected_user_csv_file ='/data/91jili/merge/export/test.migrate_user.csv'; 
      $this->assertFileExists($expected_user_csv_file); 
      $return = str_getcsv(file_get_contents($expected_user_csv_file));
      $this->assertEquals('NULL', $return[19], 'identity_num');
      $this->after_test();
  }


  function test_generate_user_data_only_jili_delete_flag_1()
  {
      $this->before_test();
// is_email_confirmed not nll
      $user_row = str_getcsv(<<<EOD
"1291363","tao_jiang@voyagegroup.com","2ef75e7c46e06b90507e4d47780fd8426857c0ab","1","1","NULL","","QQ懂你","2","1988-1","13052550759","NULL","3","18","NULL","NULL","NULL","1,9,11","NULL","NULL","1","2015-01-24 10:29:25","2015-01-24 10:29:25","11.22.33.44","77","1","1","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL"
EOD
);
      generate_user_data_only_jili($user_row);
      $expected_user_csv_file ='/data/91jili/merge/export/test.migrate_user.csv'; 
      $this->assertFileExists($expected_user_csv_file); 
      $return = str_getcsv(file_get_contents($expected_user_csv_file));
      $this->assertEquals('1', $return[25], 'delete flag is 1');
      $this->after_test();
  }

  function test_generate_user_data_only_jili_is_info_set_0()
  {
      $this->before_test();
// is_email_confirmed not nll
      $user_row = str_getcsv(<<<EOD
"1291363","tao_jiang@voyagegroup.com","2ef75e7c46e06b90507e4d47780fd8426857c0ab","1","1","NULL","","QQ懂你","2","1988-1","13052550759","NULL","3","18","NULL","NULL","NULL","1,9,11","NULL","NULL","1","2015-01-24 10:29:25","2015-01-24 10:29:25","11.22.33.44","77","1","0","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL"
EOD
);
      generate_user_data_only_jili($user_row);
      $expected_user_csv_file ='/data/91jili/merge/export/test.migrate_user.csv'; 
      $this->assertFileExists($expected_user_csv_file); 
      $return = str_getcsv(file_get_contents($expected_user_csv_file));
      $this->assertEquals('0', $return[26], 'is info set is 0');
      $this->after_test();
  }

  function test_generate_user_data_wenwen_common()
  {
    $this->markTestIncomplete(
      'This test has not been implemented yet.'
    );

    $this->before_test();

    $return = generate_user_data_wenwen_common(array(), array());
    $this->assertEquals('', $return);

//"id","panel_region_id","panel_id","email","login_id","login_password","login_password_crypt_type","login_password_salt","updated_at","created_at","created_remote_addr","created_user_agent","login_valid_flag","sex_code","birthday","panelist_status","campaign_code","last_login_time"

    $panelist_row = str_getcsv(<<<EOD
"6","2000","2","tao.jiang@d8aspring.com","NULL","DIqpJ2jiaHM=","blowfish","76acb8b7f6d767bdf6955c02f0a7c128","2011-02-25 19:42:21","2009-10-30 10:44:21","116.228.205.38","Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0; .NET CLR 2.0.50727; InfoPath.2; .NET","1","1","1981-08-04","2","offer99","2013-12-19 17:48:55"

EOD
) ;
//"id","email","pwd","is_email_confirmed","is_from_wenwen","wenwen_user","token","nick","sex","birthday","tel","is_tel_confirmed","province","city","education","profession","income","hobby","personalDes","identity_num","reward_multiple","register_date","last_login_date","last_login_ip","points","delete_flag","is_info_set","icon_path","uniqkey","token_created_at","origin_flag","created_remote_addr","created_user_agent","campaign_code","password_choice"
    $user_row = str_getcsv(<<<EOD
"1291365","tao_jiang@voyagegroup.com","2ef75e7c46e06b90507e4d47780fd8426857c0ab","","1","NULL","","QQ懂你","2","1988-1","13052550759","NULL","3","18","NULL","NULL","NULL","1,9,11","NULL","NULL","1","2015-01-24 10:29:25","2015-01-24 10:29:25","11.22.33.44","77","0","1","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL"
EOD
) ;

    $return = generate_user_data_wenwen_common($panelist_row, $user_row);

    $this->assertCount(39, $return, 'merged user array has 39 items');
    
    $this->assertEquals('1291365', $return[0],'merged user  id'); 
    $this->assertEquals('tao.jiang@d8asrping.com', $return[1],'merged user  email');
    $this->assertEquals('2ef75e7c46e06b90507e4d47780fd8426857c0ab', $return[2],'merged user  pwd');
    $this->assertEquals('1', $return[3],'is_email_confirmed, panelist email is confirmed ');
    $this->assertEquals('1', $return[4], 'is_from_wenwen'); // 1, 2 NULL
    $this->assertEquals('NULL', $return[5], 'wenwen_user');
    $this->assertEquals('', $return[6], 'token');
    $this->assertEquals('"QQ懂你"', $return[7], 'nick');
    $this->assertEquals('2', $return[8], 'sex');
    $this->assertEquals('1988-1', $return[9], 'birthday');
    $this->assertEquals('13052550759', $return[10],'tel');
    $this->assertEquals('NULL', $return[11],'is_tel_confirmed');
    $this->assertEquals('1', $return[12],'province for panelist.panel_region_id 1');
    $this->assertEquals('2', $return[13],'city panelist.panel_region_id 1');
    $this->assertEquals('5', $return[14],'education'); // 5  '5': "研究生，博士毕业"
    $this->assertEquals('99', $return[15],'profession'); // 99: 其它
    $this->assertEquals('20', $return[16],'income'); // 360000 +
    $this->assertEquals('1,9,11', $return[17],'hobby'); // 
    $this->assertEquals('出生:毕业:工作:经历:', $return[18],'personalDes');
    $this->assertEquals('NULL', $return[19],'identity_num');
    $this->assertEquals('1', $return[20], 'reward_multiple');
    $this->assertEquals('2009-10-30 10:44:21', $return[21], 'register_date');
    $this->assertEquals('2013-12-19 17:48:55', $return[22], 'last_login_date');
    $this->assertEquals('11.22.33.44', $return[23], 'last_login_ip use jili ? ');
    $this->assertEquals('88', $return[24], 'points sum both');
    $this->assertEquals('0', $return[25], 'delete_flag');
    $this->assertEquals('1', $return[26], 'is_info_set');
//    $this->assertEquals('', $return[27], 'icon_path');
    $this->assertEquals('NULL', $return[28], 'uniqkey');
    $this->assertEquals('NULL', $return[29], 'token_created_at');
    $this->assertEquals('3', $return[30], 'origin_flag');
    $this->assertEquals('116.228.205.38', $return[31], 'created_remote_addr');
    $this->assertEquals('Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0; .NET CLR 2.0.50727; InfoPath.2; .NET', $return[32], 'created_user_agent');
    $this->assertEquals('offer99', $return[33], 'campaign_code');
    $this->assertEquals('1', $return[34], 'password_choice');
    $this->assertEquals('都一般', $return[35], '喜欢的音乐');
    $this->assertEquals('30', $return[36], '行业detail.industry_code');  
    $this->assertEquals('16', $return[37], '部门detail.work_section_code'); 
    $this->assertEquals('要不中个500万玩玩？', $return[38], '本月心愿');

    $this->after_test();

  }



  function test_generate_user_wenwen_login_data()
  {
    $this->markTestIncomplete(
      'This test has not been implemented yet.'
    );
     $fh = fopen('php://memory','r+');
     fwrite($fh, <<<EOD
region_id,province_id,city_id
2000,1,2
2001,1,2
2352,31,360
2353,31,361
2354,31,362
2355,32,363
EOD
);

    $index = build_file_index($fh, 'region_id');
$this->assertCount(6, $index, 'region_mapping index' );
     fclose($fh);


  }

  function test_export_csv()
  {
    $this->markTestIncomplete(
      'This test has not been implemented yet.'
    );

  }

  function test_build_file_index( ) 
  {
    $fh = fopen('php://memory','r+');
    fwrite($fh, <<<EOD
id,panelist_id,nickname,show_sex,show_birthday,biography,hobby,fav_music,monthly_wish,website_url,updated_at,created_at
14436,2471,安森,1,1,"我想所有的事情都有它结束的劫数，
正如我手中的每一枚香烟。
当无法延续的时候，
就要懂得放手，懂得结束。可我并不潇洒
我总是将烟放下之后，还舍不得熄灭",音乐,轻音乐,顺顺利利就好,,"2010-12-22 13:16:27","2010-12-22 13:16:11"
80229,2720,落翎葬,0,0,"不如意事常八九，可与人言无二三
",看书,,,,"2011-10-31 22:56:10","2011-10-31 22:56:10"
30195,137921,Goking_Bo,1,1," __呮想咹咹靜靜   
　   ‘*..★'*
　     ☆　 　★
   ★.＠＠.＠＠.＠＠.★
　    ＼﹋★★﹋/ /／       
         開開❤❤",篮球，足球，看书，音乐,很多,希望心里的TA健康！,,"2011-03-05 11:43:35","2011-03-05 11:43:35"
14373,142703,海无颜,1,0,"                                         鸟啼花落
                                         皆与神通
                                         人不能悟
                                         付之飘风",,,,,"2010-12-22 11:48:16","2010-12-22 11:48:16"
3079,466118,思念远方的你,1,1,"


看完《投名状》发现，兄弟靠不住 

看完《集结号》发现，组织靠不住 

看完《妈妈再爱我一次》发现，老爸靠不住 

看完《新警察故事》发现，儿子靠不住 

看完《满城尽带黄金甲》发现，老婆老公靠不住 

看完《红楼梦》发现，祖母和嫂子也靠不住 


看完《西游记》发现，师傅靠不住 

看完《霍元甲》发现，徒弟靠不住 

看完《无间道》发现，警察靠不住 

看完《水浒》发现，领导靠不住 

看完《肖申克的救赎》发现，出纳靠不住 

看完《史密斯夫妇》发现，公司也是靠不住的 


看完《无极》发现，馒头靠不住 

看完《青蛇》发现，动物靠不住 

看完《越狱》发现，牢房靠不住 

看完《阳光灿烂的日子》发现，避孕套靠不住 

看完《午夜凶铃》发现，电话靠不住，电视更靠不住 

看完《疯狂的石头》发现，国际高手是靠不住滴 


看完《长江7号》发现，地球人靠不住 

看完《变形金刚》发现，外星人也靠不住 

看完《黑客帝国》发现，一切现实都靠不住 

结论：只有“我”靠得住 

",玩,伤感歌曲,找到一个自己爱的人,,"2010-12-14 18:06:47","2010-12-14 18:06:47"
2225,484258,简荇,1,0,"瀞瀞dê ⒈個人︵.o.︵.o.

          、 '╅`、    站在⑽字架喕前```
        、‖  
             
ゃ.        __‖ __、       黙黙許願..  

你要幸福...!",,,,,"2010-12-14 20:36:24","2010-12-14 12:53:41"
EOD
);

    $index = build_file_index($fh);
    $this->assertCount(6, $index,'lines with carriage');
    fclose($fh);
    $this->assertArrayHasKey('2471', $index, 'panelist_id  ');
    $this->assertArrayHasKey('2720', $index, 'panelist_id  ');
    $this->assertArrayHasKey('137921', $index, 'panelist_id  ');
    $this->assertArrayHasKey('466118', $index, 'panelist_id  ');
    $this->assertArrayHasKey('484258', $index, 'panelist_id  ');

    $fh = fopen('php://memory','r+');
    fwrite($fh, <<<EOD
"panelist_id","mobile_number","status_flag","updated_at","created_at"
"6","13052550759","1","2012-10-20 13:13:01","2012-10-20 13:13:01"
"2230806","17715018917","1","2015-11-17 17:16:38","2015-11-17 17:16:38"

EOD
);

    $return = build_file_index($fh, 'mobile_number');

    $this->assertCount(2, $return);
    $this->assertArrayHasKey('13052550759', $return, 'panelist_id  6 as key');
    $this->assertArrayHasKey('17715018917', $return, 'panelist_id  6 as key');
    fseek($fh, $return['13052550759']);
    $this->assertEquals('"6","13052550759","1","2012-10-20 13:13:01","2012-10-20 13:13:01"'.PHP_EOL, fgets($fh) , 'the 1st data row ');
    fseek($fh, $return['17715018917']);
    $this->assertEquals('"2230806","17715018917","1","2015-11-17 17:16:38","2015-11-17 17:16:38"'.PHP_EOL, fgets($fh) , 'the 1st data row ');

    $return = build_file_index($fh);

    $this->assertCount(2, $return);
    $this->assertArrayHasKey(6, $return, 'panelist_id  6 as key');
    $this->assertArrayHasKey(2230806, $return, 'panelist_id  6 as key');
    fseek($fh, $return[6]);
    $this->assertEquals('"6","13052550759","1","2012-10-20 13:13:01","2012-10-20 13:13:01"'.PHP_EOL, fgets($fh) , 'the 1st data row ');
    fseek($fh, $return[2230806]);
    $this->assertEquals('"2230806","17715018917","1","2015-11-17 17:16:38","2015-11-17 17:16:38"'.PHP_EOL, fgets($fh) , 'the 1st data row ');

    fclose($fh);

    $fh = fopen('php://memory','r+');
    fwrite($fh, <<<EOD
"id","panelist_id","nickname","show_sex","show_birthday","biography","hobby","fav_music","monthly_wish","website_url","updated_at","created_at"
"2255","6","琪琪琪","1","1","","数码控","都一般","要不中个500万玩玩？","NULL","2010-12-14 13:03:21","2010-12-14 13:03:21"
"412569","2230879","xingting520","0","0","NULL","NULL","NULL","NULL","NULL","2015-11-17 17:38:39","2015-11-17 17:38:39"
EOD
);
    $return = build_file_index($fh);
    $this->assertCount(2, $return);
    $this->assertArrayHasKey(6, $return, 'panelist_id  6 as key');
    $this->assertArrayHasKey(2230879, $return, 'panelist_id  6 as key');

    fseek($fh, $return[6]);
    $this->assertEquals('"2255","6","琪琪琪","1","1","","数码控","都一般","要不中个500万玩玩？","NULL","2010-12-14 13:03:21","2010-12-14 13:03:21"'.PHP_EOL, 
      fgets($fh) , 'the 1st data row ');
    fseek($fh, $return[2230879]);
    $this->assertEquals('"412569","2230879","xingting520","0","0","NULL","NULL","NULL","NULL","NULL","2015-11-17 17:38:39","2015-11-17 17:38:39"', fgets($fh) , 'the 1st data row ');

    fclose($fh);

    $fh = fopen('php://memory','r+');
    fwrite($fh, <<<EOD
id,panelist_id,nickname,show_sex,show_birthday,biography,hobby,fav_music,monthly_wish,website_url,updated_at,created_at
2255,23,钱琪祯,1,1,,数码控,都一般,要不中个500万玩玩？,,"2010-12-14 13:03:21","2010-12-14 13:03:21"
4845,27,螃蟹,1,1,,,,,,"2010-12-15 16:07:46","2010-12-15 16:07:46"
127023,31,千平,1,1,想去旅游，不想上班,上网、宅、旅游、狗狗控,所有好听的,想去旅游，不想上班,,"2012-03-15 16:21:55","2012-03-15 15:45:51"
51466,42,犄角旮旯,1,1,行走在光影中的,摄影,摇滚,过好每一天,,"2011-07-21 12:57:35","2011-07-21 12:57:35"
348,43,Tiger,1,1,,,,,,"2010-12-18 11:12:18","2010-12-13 22:21:24"
40701,52,janetyuan,0,0,一切如愿呀！,休闲、逛街,刘若英,身体棒棒,,"2011-05-31 17:25:01","2011-05-31 17:25:01"
9520,121,布袋熊,1,0,,,,,,"2010-12-19 00:07:05","2010-12-19 00:07:05"
6641,156,冰之雪,1,0,你们好，我很喜欢交友,交友，看书,,,,"2010-12-17 12:25:26","2010-12-17 12:25:26"
29382,177,恋爱一生,0,0,,看书,做我老婆好不好,买一台本本,,"2011-02-28 15:45:06","2011-02-28 15:45:06"
419130,2240859,dj小陈20,0,0,,,,,,"2015-12-02 15:43:36","2015-12-02 15:43:36"
419131,2240860,ddlsn,0,0,,,,,,"2015-12-02 15:43:58","2015-12-02 15:43:58"
419132,2240862,nana666,0,0,,,,,,"2015-12-02 15:44:53","2015-12-02 15:44:53"
419133,2240863,多拉拉,0,0,,,,,,"2015-12-02 15:45:45","2015-12-02 15:45:45"
419134,2240864,LiangD,0,0,,,,,,"2015-12-02 15:46:04","2015-12-02 15:46:04"
419135,2240865,wood2508,0,0,,,,,,"2015-12-02 15:47:39","2015-12-02 15:47:39"
419136,2240867,jyq33404,0,0,,,,,,"2015-12-02 15:48:03","2015-12-02 15:48:03"
419137,2240868,linlin0927,0,0,,,,,,"2015-12-02 15:50:22","2015-12-02 15:50:22"
419138,2240869,陈根顺,0,0,,,,,,"2015-12-02 15:52:03","2015-12-02 15:52:03"
419139,2240870,翁乐天,0,0,,,,,,"2015-12-02 15:52:46","2015-12-02 15:52:46"
EOD
);

    $index = build_file_index($fh);
    $this->assertCount(19, $index);
    $this->assertArrayHasKey(2240870, $index, 'panelist_id  419139 as key');
    $this->assertArrayHasKey(23, $index, 'panelist_id  2255 as key');

    fseek($fh, $index[2240870]);
    $this->assertEquals('419139,2240870,翁乐天,0,0,,,,,,"2015-12-02 15:52:46","2015-12-02 15:52:46"', 
      fgets($fh) , 'the 1st data row ');


    fseek($fh, $index[23]);
    $this->assertEquals('2255,23,钱琪祯,1,1,,数码控,都一般,要不中个500万玩玩？,,"2010-12-14 13:03:21","2010-12-14 13:03:21"'.PHP_EOL, fgets($fh) , 'the 1st data row ');

    fclose($fh);
  }

  function test_build_key_value_index() 
  {
    $fh = fopen('php://memory','r+');
    fwrite($fh, <<<EOD
id,user_id,created_at,email
1,91,"2014-11-17 14:56:30","xujf@voyagegroup.com.cn"
2,1051021,"2014-11-17 14:58:23","miaomiao.zhang@d8aspring.com"
3,110,"2014-11-17 15:05:10","takafumi_sekiguchi@researchpanelasia.com"
4,1206052,"2014-11-20 16:53:54","2442092961@qq.com"
5,1264810,"2014-11-20 16:55:45","704617264@qq.com"
6,1257149,"2014-11-20 16:55:50","515776213@qq.com"
7,1267542,"2014-11-20 16:56:05","2605990968@qq.com"
8,1085696,"2014-11-20 16:56:44","tangqing1984@126.com"
9,1266832,"2014-11-20 16:57:23","1627958274@qq.com"
61424,1437347,"2015-11-17 15:01:04","58073288@qq.com"
61425,1437413,"2015-11-17 15:21:08","383589666@qq.com"
61426,1437443,"2015-11-17 15:36:02","z863437758@163.com"
61427,1436474,"2015-11-17 15:52:19","hailong.719@163.com"
61428,1437325,"2015-11-17 15:52:32","604124403@qq.com"
61429,1437428,"2015-11-17 15:53:57","allykua66@163.com"
61430,1437434,"2015-11-17 16:16:49","lieyanhanbing810@163.com"
61431,1436638,"2015-11-17 16:29:11","861522677@qq.com"
61432,1437472,"2015-11-17 16:32:01","dfln@qq.com"
61433,1421745,"2015-11-17 16:40:33","854799320@qq.com"
EOD
);

    $return = build_key_value_index($fh, 'id', 'email');

    $this->assertCount(19, $return );
    $this->assertEquals("xujf@voyagegroup.com.cn", $return[1] ['email']);
    $this->assertEquals("854799320@qq.com", $return["61433"] ['email']);
    $this->assertEquals("854799320@qq.com", $return[61433] ['email']);


    $return = build_key_value_index($fh,  'email','id');
    $this->assertCount(19, $return );
    $this->assertEquals(1, $return["xujf@voyagegroup.com.cn"] ['id']);
    $this->assertEquals(61433, $return["854799320@qq.com"] ['id']);

    $return = build_key_value_index($fh,  'user_id','id');
    $this->assertCount(19, $return );
    $this->assertEquals(1, $return["91"] ['id']);
    $this->assertEquals(61433, $return[1421745] ['id']);


    $return = build_key_value_index($fh,  'id','user_id');
    $this->assertCount(19, $return );
    $this->assertEquals(91, $return[1] ['user_id']);
    $this->assertEquals(1421745, $return[61433] ['user_id']);

    $return = build_key_value_index($fh,  'id_','user_id');
    $this->assertNull($return);

    $return = build_key_value_index($fh,  'email','user_id');
    $this->assertCount(19, $return );
    $this->assertEquals(91, $return['xujf@voyagegroup.com.cn'] ['user_id']);
    $this->assertEquals(1421745, $return["854799320@qq.com"] ['user_id']);


    fclose($fh);


    $fh = fopen('php://memory','r+');
    fwrite($fh, <<<EOD
"id","user_id","created_at","email"
"1","91","2014-11-17 14:56:30","xujf@voyagegroup.com.cn"
"2","1051021","2014-11-17 14:58:23","miaomiao.zhang@d8aspring.com"
"3","110","2014-11-17 15:05:10","takafumi_sekiguchi@researchpanelasia.com"
"4","1206052","2014-11-20 16:53:54","2442092961@qq.com"
"5","1264810","2014-11-20 16:55:45","704617264@qq.com"
"6","1257149","2014-11-20 16:55:50","515776213@qq.com"
"7","1267542","2014-11-20 16:56:05","2605990968@qq.com"
"8","1085696","2014-11-20 16:56:44","tangqing1984@126.com"
"9","1266832","2014-11-20 16:57:23","1627958274@qq.com"
"61424","1437347","2015-11-17 15:01:04","58073288@qq.com"
"61425","1437413","2015-11-17 15:21:08","383589666@qq.com"
"61426","1437443","2015-11-17 15:36:02","z863437758@163.com"
"61427","1436474","2015-11-17 15:52:19","hailong.719@163.com"
"61428","1437325","2015-11-17 15:52:32","604124403@qq.com"
"61429","1437428","2015-11-17 15:53:57","allykua66@163.com"
"61430","1437434","2015-11-17 16:16:49","lieyanhanbing810@163.com"
"61431","1436638","2015-11-17 16:29:11","861522677@qq.com"
"61432","1437472","2015-11-17 16:32:01","dfln@qq.com"
"61433","1421745","2015-11-17 16:40:33","854799320@qq.com"

EOD
);
    $return = build_key_value_index($fh, 'id', 'email');

    $this->assertCount(19, $return );
    $this->assertEquals("xujf@voyagegroup.com.cn", $return[1] ['email']);
    $this->assertEquals("854799320@qq.com", $return["61433"] ['email']);
    $this->assertEquals("854799320@qq.com", $return[61433] ['email']);
        
    $return = build_key_value_index($fh,  'email','id');
    $this->assertCount(19, $return );
    $this->assertEquals(1, $return["xujf@voyagegroup.com.cn"] ['id']);
    $this->assertEquals(61433, $return["854799320@qq.com"] ['id']);

    $return = build_key_value_index($fh,  'user_id','id');
    $this->assertCount(19, $return );
    $this->assertEquals(1, $return["91"] ['id']);
    $this->assertEquals(61433, $return[1421745] ['id']);


    $return = build_key_value_index($fh,  'id','user_id');
    $this->assertCount(19, $return );
    $this->assertEquals(91, $return[1] ['user_id']);
    $this->assertEquals(1421745, $return[61433] ['user_id']);

    $return = build_key_value_index($fh,  'id_','user_id');
    $this->assertNull($return);

    $return = build_key_value_index($fh,  'email','user_id');
    $this->assertCount(19, $return );
    $this->assertEquals(91, $return['xujf@voyagegroup.com.cn'] ['user_id']);
    $this->assertEquals(1421745, $return["854799320@qq.com"] ['user_id']);

    fclose($fh);

  }


  function test_use_file_index() 
  {

    $fh = fopen('php://memory','r+');
    fwrite($fh, <<<EOD
id,panelist_id,nickname,show_sex,show_birthday,biography,hobby,fav_music,monthly_wish,website_url,updated_at,created_at
32856,11036,我心永飞翔,1,1,潜心修炼,"足球\旅游",白狐,和阿娟在一起,,"2011-04-19 09:00:18","2011-04-19 09:00:05"
EOD
);
    $index = build_file_index($fh);

    $return = use_file_index( $index, 11036,$fh , false);
    $this->assertCount(12, $return,'csv array return' );


    $fh = fopen('php://memory','r+');
    fwrite($fh, <<<EOD
id,panelist_id,nickname,show_sex,show_birthday,biography,hobby,fav_music,monthly_wish,website_url,updated_at,created_at
2255,6,"琪琪琪",1,1,,"数码控","都一般","要不中个500万玩玩？",NULL,"2010-12-14 13:03:21","2010-12-14 13:03:21"
412569,2230879,"xingting520",0,0,,,,,,"2015-11-17 17:38:39","2015-11-17 17:38:39"
EOD
);
    $index = build_file_index($fh);

    $return = use_file_index( $index, 2230879 ,$fh , false);
    $this->assertCount(2, $index);
    $this->assertCount(12, $return,'csv array return' );

    $this->assertEquals(412569, $return[0],'csv array return' );
    $this->assertEquals('2015-11-17 17:38:39', $return[11],'csv array return' );

    $return = use_file_index( $index, 6,$fh , true);
    $this->assertCount(1, $index);
    fclose($fh);

    $fh = fopen('php://memory','r+');
    fwrite($fh, <<<EOD
"id","panelist_id","nickname","show_sex","show_birthday","biography","hobby","fav_music","monthly_wish","website_url","updated_at","created_at"
"2255","6","琪琪琪","1","1","","数码控","都一般","要不中个500万玩玩？","NULL","2010-12-14 13:03:21","2010-12-14 13:03:21"
"412569","2230879","xingting520","0","0","NULL","NULL","NULL","NULL","NULL","2015-11-17 17:38:39","2015-11-17 17:38:39"
EOD
);
    $index = build_file_index($fh);

    $return = use_file_index( $index, 2230879 ,$fh , false);
    $this->assertCount(2, $index);
    $this->assertCount(12, $return,'csv array return' );

    $this->assertEquals(412569, $return[0],'csv array return' );
    $this->assertEquals('2015-11-17 17:38:39', $return[11],'csv array return' );

    $return = use_file_index( $index, 6,$fh , true);
    $this->assertCount(1, $index);

    fclose($fh);

    $fh = fopen('php://memory','r+');
    fwrite($fh, <<<EOD
id,panelist_id,nickname,show_sex,show_birthday,biography,hobby,fav_music,monthly_wish,website_url,updated_at,created_at
2255,23,钱琪祯,1,1,,数码控,都一般,要不中个500万玩玩？,,"2010-12-14 13:03:21","2010-12-14 13:03:21"
4845,27,螃蟹,1,1,,,,,,"2010-12-15 16:07:46","2010-12-15 16:07:46"
127023,31,千平,1,1,想去旅游，不想上班,上网、宅、旅游、狗狗控,所有好听的,想去旅游，不想上班,,"2012-03-15 16:21:55","2012-03-15 15:45:51"
51466,42,犄角旮旯,1,1,行走在光影中的,摄影,摇滚,过好每一天,,"2011-07-21 12:57:35","2011-07-21 12:57:35"
348,43,Tiger,1,1,,,,,,"2010-12-18 11:12:18","2010-12-13 22:21:24"
40701,52,janetyuan,0,0,一切如愿呀！,休闲、逛街,刘若英,身体棒棒,,"2011-05-31 17:25:01","2011-05-31 17:25:01"
9520,121,布袋熊,1,0,,,,,,"2010-12-19 00:07:05","2010-12-19 00:07:05"
6641,156,冰之雪,1,0,你们好，我很喜欢交友,交友，看书,,,,"2010-12-17 12:25:26","2010-12-17 12:25:26"
29382,177,恋爱一生,0,0,,看书,做我老婆好不好,买一台本本,,"2011-02-28 15:45:06","2011-02-28 15:45:06"
419130,2240859,dj小陈20,0,0,,,,,,"2015-12-02 15:43:36","2015-12-02 15:43:36"
419131,2240860,ddlsn,0,0,,,,,,"2015-12-02 15:43:58","2015-12-02 15:43:58"
419132,2240862,nana666,0,0,,,,,,"2015-12-02 15:44:53","2015-12-02 15:44:53"
419133,2240863,多拉拉,0,0,,,,,,"2015-12-02 15:45:45","2015-12-02 15:45:45"
419134,2240864,LiangD,0,0,,,,,,"2015-12-02 15:46:04","2015-12-02 15:46:04"
419135,2240865,wood2508,0,0,,,,,,"2015-12-02 15:47:39","2015-12-02 15:47:39"
51780,654709,小刺一一,1,1,"谢谢你们
我的名字叫做小刺。",旅游,很多,赚钱,,"2011-07-22 21:28:14","2011-07-22 21:28:14"
51735,654510,李江华,1,1,"如果不坚强，懦弱给谁？
",打球，篮球,dj等等吧..,还没呢,,"2011-07-22 17:44:27","2011-07-22 17:44:27"
51844,654285,chanel,1,1,"c
V ","music ","i swear"," buy popular telphone ",,"2011-07-23 09:49:55","2011-07-23 09:49:55"
51633,654159,蜗牛_木木,1,1,"为了儿子为了生活
",12,12,有花不完的钱,,"2011-07-27 12:18:36","2011-07-22 12:26:46"
419139,2240870,翁乐天,0,0,,,,,,"2015-12-02 15:52:46","2015-12-02 15:52:46"
EOD
);

    $index = build_file_index($fh);

    $row = use_file_index($index,177 , $fh, true);
    $this->assertCount(12, $row,'profile has  12 items');

    $row = use_file_index($index,654159, $fh, true);

    $this->assertCount(12, $row, 'profile has  12 items');

    $row = use_file_index($index,654285, $fh, true);
    $this->assertCount(12, $row, 'profile has  12 items');

    $row = use_file_index($index,654709, $fh, true);
    $this->assertCount(12, $row, 'profile has  12 items');
    fclose($fh);
  }

  function test_use_key_value_index() 
  {
    $fh = fopen('php://memory','r+');
    fwrite($fh, <<<EOD
"id","user_id","created_at","email"
"1","91","2014-11-17 14:56:30","xujf@voyagegroup.com.cn"
"2","1051021","2014-11-17 14:58:23","miaomiao.zhang@d8aspring.com"
"3","110","2014-11-17 15:05:10","takafumi_sekiguchi@researchpanelasia.com"
"4","1206052","2014-11-20 16:53:54","2442092961@qq.com"
"5","1264810","2014-11-20 16:55:45","704617264@qq.com"
"6","1257149","2014-11-20 16:55:50","515776213@qq.com"
"7","1267542","2014-11-20 16:56:05","2605990968@qq.com"
"8","1085696","2014-11-20 16:56:44","tangqing1984@126.com"
"9","1266832","2014-11-20 16:57:23","1627958274@qq.com"
"61424","1437347","2015-11-17 15:01:04","58073288@qq.com"
"61425","1437413","2015-11-17 15:21:08","383589666@qq.com"
"61426","1437443","2015-11-17 15:36:02","z863437758@163.com"
"61427","1436474","2015-11-17 15:52:19","hailong.719@163.com"
"61428","1437325","2015-11-17 15:52:32","604124403@qq.com"
"61429","1437428","2015-11-17 15:53:57","allykua66@163.com"
"61430","1437434","2015-11-17 16:16:49","lieyanhanbing810@163.com"
"61431","1436638","2015-11-17 16:29:11","861522677@qq.com"
"61432","1437472","2015-11-17 16:32:01","dfln@qq.com"
"61433","1421745","2015-11-17 16:40:33","854799320@qq.com"

EOD
);
    $index = build_key_value_index($fh, 'id', 'email');

    $return = use_key_value_index($index, 61431, false );
    $this->assertCount(19, $index);
    $this->assertEquals('861522677@qq.com' ,$return['email'], ' found email of id 61431');
    $return = use_key_value_index($index, 61431  );
    $this->assertCount(18, $index);

    fclose($fh);
  }

  private function after_test() 
  {
    global $panelist_mobile_indexs;
    global $region_mapping_indexs;
    global $panelist_detail_indexs;
    global $panelist_profile_indexs;
    global $panelist_point_indexs;
    global $panelist_image_indexs;

    unset($panelist_mobile_indexs);
    unset($region_mapping_indexs);
    unset($panelist_detail_indexs);
    unset($panelist_profile_indexs);
    unset($panelist_point_indexs);
    unset($panelist_image_indexs);



    global $panelist_mobile_number_file_handle ;
    global $migration_region_mapping_file_handle ;
    global $panelist_detail_file_handle ;
    global $panelist_profile_file_handle ;
    global $panelist_point_file_handle ;
    global $panelist_profile_image_file_handle ;

    fclose( $panelist_mobile_number_file_handle );
    fclose( $migration_region_mapping_file_handle );
    fclose( $panelist_detail_file_handle );
    fclose( $panelist_profile_file_handle );
    fclose( $panelist_point_file_handle );
    fclose( $panelist_profile_image_file_handle );
  }

  private function before_test() 
  {
      global $panelist_mobile_number_file_handle ;
      global $migration_region_mapping_file_handle ;
      global $panelist_detail_file_handle ;
      global $panelist_profile_file_handle ;
      global $panelist_point_file_handle ;
      global $panelist_profile_image_file_handle ;



      $panelist_mobile_number_file_handle = fopen('php://memory','r+');
      $migration_region_mapping_file_handle = fopen('php://memory','r+');
      $panelist_detail_file_handle = fopen('php://memory','r+');
      $panelist_profile_file_handle = fopen('php://memory','r+');
      $panelist_point_file_handle = fopen('php://memory','r+');
      $panelist_profile_image_file_handle = fopen('php://memory','r+');


      fwrite($panelist_mobile_number_file_handle,<<<EOD
"panelist_id","mobile_number","status_flag","updated_at","created_at"
"6","13052550759","1","2012-10-20 13:13:01","2012-10-20 13:13:01"
"2230806","17715018917","1","2015-11-17 17:16:38","2015-11-17 17:16:38"
EOD
);

     fwrite($migration_region_mapping_file_handle,<<<EOD
"region_id","province_id","city_id"
"2000","1","2"
"2002","1","1"
"2355","32","363"
EOD
) ;

     fwrite($panelist_detail_file_handle,<<<EOD
"panelist_id","name_first","name_middle","name_last","furigana_first","furigana_middle","furigana_last","age","zip1","zip2","address1","address2","address3","home_type_code","home_year","tel1","tel2","tel3","tel_mobile1","tel_mobile2","tel_mobile3","mobile_number","marriage_code","child_code","child_num","income_family_code","income_personal_code","job_code","industry_code","work_section_code","graduation_code","industry_code_family","internet_starttime_code","internet_usetime_code","last_answer_date","updated_at","created_at"
"6","广广广广","NULL","祥广","NULL","NULL","NULL","NULL","","","","taiwan","","0","","NULL","NULL","NULL","NULL","NULL","NULL","010101010","NULL","NULL","NULL","NULL","20","4","3","9","3","NULL","NULL","NULL","2010-01-18 11:31:22","2010-01-19 17:53:20","2009-10-30 09:41:38"
"2230880","zyatwork","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","NULL","20","99","30","16","5","NULL","NULL","NULL","NULL","2015-11-17 17:39:49","2015-11-17 17:39:49"
EOD
);


     fwrite($panelist_profile_file_handle, <<<EOD
"id","panelist_id","nickname","show_sex","show_birthday","biography","hobby","fav_music","monthly_wish","website_url","updated_at","created_at"
"2255","6","琪琪琪","1","1","出生:毕业:工作:经历:","数码控","都一般","要不中个500万玩玩？","NULL","2010-12-14 13:03:21","2010-12-14 13:03:21"
"412569","2230879","xingting520","0","0","NULL","NULL","NULL","NULL","NULL","2015-11-17 17:38:39","2015-11-17 17:38:39"
EOD
) ;


     fwrite($panelist_point_file_handle, <<<EOD
"panelist_id","point_value","last_add_time","last_add_log_yyyymm","last_add_log_id","last_active_time","updated_at","created_at"
"6","11","2014-01-10 18:06:59","new","0","NULL","2014-01-10 18:06:59","2014-01-10 18:06:59"
"2230880","12","2015-11-17 17:41:00","201511","854791","2015-11-17 17:41:00","2015-11-17 17:41:00","2015-11-17 17:39:49"
EOD
) ;


     fwrite($panelist_profile_image_file_handle,<<<EOD
"panelist_id","hash","s_file","s_width","s_height","m_file","m_width","m_height","l_file","l_width","l_height","delete_flag","updated_at","created_at"
"6","c05fc2fdb476d327e418b9950ba89c32c443394c","c/0/5/c05fc2fdb476d327e418b9950ba89c32c443394c_s.jpg","30","30","c/0/5/c05fc2fdb476d327e418b9950ba89c32c443394c_m.jpg","90","90","c/0/5/c05fc2fdb476d327e418b9950ba89c32c443394c_l.jpg","270","270","0","2012-12-31 10:24:28","2012-12-31 10:23:15"
"2230654","a58b61794e61191590bafb832c3fe29cd11c0eb1","a/5/8/a58b61794e61191590bafb832c3fe29cd11c0eb1_s.jpg","30","30","a/5/8/a58b61794e61191590bafb832c3fe29cd11c0eb1_m.jpg","90","90","a/5/8/a58b61794e61191590bafb832c3fe29cd11c0eb1_l.jpg","270","270","0","2015-11-17 14:15:00","2015-11-17 14:14:28"
EOD
);


    global $panelist_mobile_indexs;
    global $region_mapping_indexs;
    global $panelist_detail_indexs;
    global $panelist_profile_indexs;
    global $panelist_point_indexs;
    global $panelist_image_indexs;


    $panelist_mobile_indexs = build_key_value_index($panelist_mobile_number_file_handle, 'panelist_id', 'mobile_number');
    $region_mapping_indexs = build_file_index($migration_region_mapping_file_handle, 'region_id');
    $panelist_detail_indexs = build_file_index($panelist_detail_file_handle, 'panelist_id');
    $panelist_profile_indexs = build_file_index($panelist_profile_file_handle, 'panelist_id');

    $panelist_point_indexs = build_key_value_index($panelist_point_file_handle, 'panelist_id', 'point_value');
    $panelist_image_indexs = build_key_value_index($panelist_profile_image_file_handle, 'panelist_id', 'hash');
  }

    public function test_strip_vote_description_links() 
    {

        $description  ='[该题由热心用户：<a href="http://www.91wenwen.net/user/152136"><font color="red">侧耳倾听</font></a> 提供，恭喜他获得了<font color="red">200</font>积分！]';
        $description_expected = '[该题由热心用户：<font color="red">侧耳倾听</font> 提供，恭喜他获得了<font color="red">200</font>积分！]';

        $this->assertEquals( $description_expected, strip_vote_description_links( $description) , 'stip the old link');


$description=<<<EOD
[该题由热心用户：<a href="http://www.91wenwen.net/user/    143704"><font color="red">吹风的鱼</font></a> 提供，恭喜他获得了<font color="red">200</font>积分！]
现如今的社会，开始起步的职场新人，都希望自己多赚一点钱而去参加兼职工作。你对此有何看法？';
EOD;

$description_expected=<<<EOD
[该题由热心用户：<font color="red">吹风的鱼</font> 提供，恭喜他获得了<font color="red">200</font>积分！]
现如今的社会，开始起步的职场新人，都希望自己多赚一点钱而去参加兼职工作。你对此有何看法？';
EOD;
        $this->assertEquals( $description_expected, strip_vote_description_links( $description) , 'stip the old link');

$description=<<<EOD
 [该题由热心用户：<a href="http://www.91wenwen.net/user/178048"><font 

color="red">缘来缘去缘如风</font></a> 提供，恭喜她获得了<font color="red">200</font>积分！]
近两年来民间借贷盛行，它可能救活了部分小企业,但也带来的负面影响。请问你对此有何看法？';
EOD;

$description_expected=<<<EOD
 [该题由热心用户：<font 

color="red">缘来缘去缘如风</font> 提供，恭喜她获得了<font color="red">200</font>积分！]
近两年来民间借贷盛行，它可能救活了部分小企业,但也带来的负面影响。请问你对此有何看法？';
EOD;
        $this->assertEquals( $description_expected, strip_vote_description_links( $description) , 'stip the old link');

$description=<<<EOD
 [该题由热心用户：<a href="http://www.91wenwen.net/user/    317558"><font color="red">待解救</font></a> 提供，恭喜她获得了<font color="red">200积分</font>！]每到年末，很多公司都会举办尾牙晚宴 ，有的比较大规模的公司甚至还会举办尾牙晚会，做下年终总结以及新的一年的公司发展前景等等。但是大多数比较铺张浪费。很多在职人员褒贬不一，你是如何看待的？
EOD;
$description_expected=<<<EOD
 [该题由热心用户：<font color="red">待解救</font> 提供，恭喜她获得了<font color="red">200积分</font>！]每到年末，很多公司都会举办尾牙晚宴 ，有的比较大规模的公司甚至还会举办尾牙晚会，做下年终总结以及新的一年的公司发展前景等等。但是大多数比较铺张浪费。很多在职人员褒贬不一，你是如何看待的？
EOD;

        $this->assertEquals( $description_expected, strip_vote_description_links( $description) , 'stip the old link');
$description=<<<EOD
 [该题由热心用户：<a href="http://www.91wenwen.net/user/honey0303 "><font color="red">344780</font></a> 提供，恭喜她获得了<font color="red">200积分</font>！]打呼噜在医学上被称为鼾症，是一种很 常见的睡眠疾病，它不仅影响他人的休息，更重要的是危害自身健康。具体有哪些危害您知道吗？
EOD;
$description_expected=<<<EOD
 [该题由热心用户：<font color="red">344780</font> 提供，恭喜她获得了<font color="red">200积分</font>！]打呼噜在医学上被称为鼾症，是一种很 常见的睡眠疾病，它不仅影响他人的休息，更重要的是危害自身健康。具体有哪些危害您知道吗？
EOD;
        $this->assertEquals( $description_expected, strip_vote_description_links( $description) , 'stip the old link');

$description=<<<EOD
 [该题由热心用户：<a href="http://www.91wenwen.net/user37069"><font color="red">pipilhp</font></a> 提供，恭喜她获得了<font color="red">200积分</font>！]很多人都采取过年在酒店订年夜饭的方式和家人一起团聚，您认为这样的方式好吗？
EOD;
$description_expected=<<<EOD
 [该题由热心用户：<font color="red">pipilhp</font> 提供，恭喜她获得了<font color="red">200积分</font>！]很多人都采取过年在酒店订年夜饭的方式和家人一起团聚，您认为这样的方式好吗？
EOD;

        $this->assertEquals( $description_expected, strip_vote_description_links( $description) , 'stip the old link');

    }
}

