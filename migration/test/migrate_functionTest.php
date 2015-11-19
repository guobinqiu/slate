<?php

include(__DIR__.'/../script/config.php');
include(__DIR__.'/../script/FileUtil.php');
include(__DIR__.'/../script/Constants.php');
include( __DIR__.'/../script/migrate_function.php');

class migrate_functionTest extends PHPUnit_Framework_TestCase
{
  public function test_getJiliConnectionByPanelistId() {

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

  function test_getPointExchangeByPanelistId() {
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
"2230880","13559988133@163.com","0","{""activation_url"":""https://www.91jili.com/user/setPassFromWenwen/ad05344a9d760c8f56f6a0d0e2873dda/1437501""}","2015-11-17 17:39:59","2015-11-17 17:39:59"
EOD
  );

    $return = getPointExchangeByPanelistId ($fh, '', $return);
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

  function test_getUser() {
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

  function test_generate_user_data_both_exsit()
  {
    $this->markTestIncomplete(
      'This test has not been implemented yet.'
    );

  }
  function test_generate_user_data_only_wenwen()
  {
    $this->markTestIncomplete(
      'This test has not been implemented yet.'
    );

  }
  function test_generate_user_data_wenwen_common()
  {
    $this->markTestIncomplete(
      'This test has not been implemented yet.'
    );

  }
  function test_generate_user_wenwen_login_data()
  {
    $this->markTestIncomplete(
      'This test has not been implemented yet.'
    );

  }
  function test_export_csv()
  {
    $this->markTestIncomplete(
      'This test has not been implemented yet.'
    );

  }

}

