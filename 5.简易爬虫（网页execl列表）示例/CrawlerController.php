<?php
namespace app\school\controller;
use think\Db;
use cmf\controller\AdminBaseController;

class CrawlerController extends AdminBaseController{

    public function crawler(){ 
		error_reporting(E_ERROR | E_PARSE );		
		$post_url = 'http://run.comaparks.com/a?login';   //登录表单提交地址
		$post = "username=admin&password=admin";//表单提交的数据（不跳过登录）
		// $cookie = "JSESSIONID=C73FF91A6FF439C073EC664E532C67E6";（跳过登录用到的cookie）

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $post_url);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);     //提交方式为post
		// curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);  //跳过登录
		$data = curl_exec($ch);
		$httpCode = curl_getinfo($ch,CURLINFO_HTTP_CODE);
   		if ($httpCode != 200) return false;
		curl_close($ch);	

		$dom = new simple_html_dom(); //php解析html类库simple_html_dom
    	$dom->load($data);  //得到html内容
		$table = $dom->find("table");  //得到table表单
		$tablename = 'SchoolBooking';    //设置数据表名
		$trarray = explode("<tr>",$table );  //以<tr>标签来分隔
		foreach ($trarray as $k=>$v){
			if ($k > 1){
				$tdarray = explode("<td>",$v );
				$data = array();
				foreach($tdarray as $k1=>$v1){
					if ($k1==1){
						$data['orderq'] = trim(strip_tags($v1));
					}
					if ($k1==2){
						if(trim(strip_tags($v1)) == "未缴费"){
							$data['payment'] = 1;
						}
						if(trim(strip_tags($v1)) == "已缴费"){
							$data['payment'] = 2;
						} 	
					}
					if ($k1==3){  
						$data['appointmentperson'] = trim(strip_tags($v1));
					}
					if ($k1==4){  
						if(trim(strip_tags($v1)) == "普通时段"){
							$data['appointmenttype'] = 1;
						}
						if(trim(strip_tags($v1)) == "VIP时段"){
							$data['appointmenttype'] = 2;
						} 
					}
					if ($k1==5){
						$data['appointmentperiod'] = trim(strip_tags($v1));
					}
					if ($k1==6){
						$data['phone'] = trim(strip_tags($v1));
					}
					if ($k1==7){
						$data['numberplate'] = trim(strip_tags($v1));
					}
                 if ($k1==8){
						$data['appointmentdate'] = strtotime(trim(strip_tags($v1)));
					}
                 if ($k1==9){
						$data['appointmentstart'] = trim(strip_tags($v1));
					}
                 if ($k1==10){
						$data['appointmentend'] = trim(strip_tags($v1));
					}
					if ($k1==11){
						$data['actualstart'] = strtotime(trim(strip_tags($v1)));
					}	
                 if ($k1==12){
						$data['actualend'] = strtotime(trim(strip_tags($v1)));
					}	
                 if ($k1==13){
                 	if(trim(strip_tags($v1)) == "预约失败"){
							$data['reservationstatus'] = 1;
						}
						if(trim(strip_tags($v1)) == "预约成功"){
							$data['reservationstatus'] = 2;
						} 
					}
					if ($k1==14){
						$data['paymentamount'] = trim(strip_tags($v1));
					}						
				}
				if($data['orderq']){
			    $info = DB::name($tablename)->where(['orderq'=>$data['orderq']])->find(); 
					if ($info){
				//更新
				Db::name($tablename)->where(['orderq'=>$data['orderq']])->update($data);
					}else{
				//插入
				Db::name($tablename)->insert($data);	 		
					}
				}				
			}
		}		
	}
}
