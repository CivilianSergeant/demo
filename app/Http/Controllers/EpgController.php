<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 10/24/2016
 * Time: 3:46 PM
 */

namespace App\Http\Controllers;


use App\Entities\ApiSetting;
use App\Entities\OrganizationInfo;
use App\Utils\AES_Engine;
use App\Utils\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use App\Entities\Epg;

class EpgController extends RestController
{
    public function index()
    {
        if (!empty($this->request)) {

            if (empty($this->request->apiName)
                && !method_exists($this, $this->request->apiName)
            ) {

                $response = new Response($this->request->apiName);
                $response->setStatus(1);
                $response->setErrorCode(100);
                $response->setErrorMsg('Api parameter apiName missing');
                return AES_Engine::getEncrypt($response, Config::get('app.encryption_key'));
            }

            try{
                $apiName = $this->request->apiName;
                return $this->$apiName();
            }catch (\Exception $ex){
                $response = new Response();
                $response->setStatus(1);
                $response->setDebugCode(100);
                $response->setDebugMsg($ex->getMessage().',Line:'.$ex->getLine());
                return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
            }


        }else{
            $response = new Response();
            $response->setStatus(1);
            $response->setDebugCode(100);
            $response->setDebugMsg("Request no found");
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }
    }

    /**
     * This method will call during
     * Get EPG API request for following url
     * http:// [ ServerIp]: [serverPort]/epgs
     * @return string
     */
    public function getEpgs()
    {
//        if(empty($this->request->contentId)){
//            $response = new Response();
//            $response->setStatus(1);
//            $response->setDebugCode(100);
//            $response->setDebugMsg("API parameter contentId is missing");
//            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
//        }

        /*if(empty($this->request->customerId)){
            $response = new Response();
            $response->setStatus(1);
            $response->setDebugCode(100);
            $response->setDebugMsg("API parameter customerId is missing");
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }

        if(empty($this->request->password)){
            $response = new Response();
            $response->setStatus(1);
            $response->setDebugCode(100);
            $response->setDebugMsg("API parameter password is missing");
            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
        }*/

//        $timezone = $this->request->timezone;
//
//        $timezone = str_split($timezone);
//        $sign = (!empty($timezone)) ? $timezone[0] : null;
//        $timeZoneHour = (isset($timezone[1]) && isset($timezone[2]))? $timezone[1].$timezone[2] : 0;
//        $timeZoneMinute = (isset($timezone[4]) && isset($timezone[5]))? $timezone[4].$timezone[5] : 0;
//
//        try {
//
//            $apiSettings = ApiSetting::find(1);
//            $organizationInfo = OrganizationInfo::find(1);
//
//            $day = strtolower(date('D',time()));
//
//
//            $select = "select id,program_id,program_name,program_description,program_logo,duration, FROM_UNIXTIME(start_time)as start_time, FROM_UNIXTIME(end_time) as end_time, GROUP_CONCAT(FROM_UNIXTIME(repeat_date_time)) as repeat_date_time
//FROM (select epgs.id,program_id,program_name,program_description,program_logo,duration,UNIX_TIMESTAMP(STR_TO_DATE(concat(show_date,\" \",start_time),'%Y-%m-%d %h:%i %p'))as start_time,
//UNIX_TIMESTAMP(STR_TO_DATE(concat(show_date,\" \",end_time),'%Y-%m-%d %h:%i %p'))as end_time,
//UNIX_TIMESTAMP(STR_TO_DATE(concat(repeat_date,\" \",repeat_time),'%Y-%m-%d %h:%i %p')) as repeat_date_time from epgs
//JOIN epg_repeat_times on epg_repeat_times.epg_id = epgs.id
//WHERE epgs.program_id = {$this->request->contentId} ) as r
//WHERE  (r.start_time >= UNIX_TIMESTAMP() OR r.end_time >= UNIX_TIMESTAMP() OR r.repeat_date_time >= UNIX_TIMESTAMP())
//UNION ALL 
//select epgs.id,program_id, program_name, program_description,program_logo,duration, 
//STR_TO_DATE(concat(Date(now()),\" \", start_time),'%Y-%m-%d %h:%i %p') as start_time, 
//STR_TO_DATE(concat(Date(now()),\" \", end_time),'%Y-%m-%d %h:%i %p') as end_time, 
//STR_TO_DATE(concat(Date(now()),\" \", repeat_time),'%Y-%m-%d %h:%i %p') as repeat_date_time
//FROM epgs
//LEFT JOIN epg_repeat_times ON epgs.id = epg_repeat_times.epg_id
//WHERE epgs.week_days LIKE '%{$day}%'
//ORDER BY start_time, repeat_date_time asc";
//
//            $epgs = DB::select($select);
//
//
//            if(!empty($epgs)){
//                foreach($epgs as $i=>$epg){
//                    $startTimestamp = strtotime($epg->start_time);
//
//                    $startTimestamp = ($sign=="+")? $startTimestamp+($timeZoneHour*60*60) : $startTimestamp-($timeZoneHour*60*60);
//                    $startTimestamp = ($sign=="+")? $startTimestamp+($timeZoneMinute*60)  : $startTimestamp-($timeZoneMinute*60);
//
//                    $endTimestamp   = strtotime($epg->end_time);
//                    $endTimestamp = ($sign=="+")? $endTimestamp+($timeZoneHour*60*60) : $endTimestamp-($timeZoneHour*60*60);
//                    $endTimestamp = ($sign=="+")? $endTimestamp+($timeZoneMinute*60)  : $endTimestamp-($timeZoneMinute*60);
//
//                    $epgs[$i]->start_time = date("Y-m-d H:i:s",$startTimestamp);
//                    $epgs[$i]->end_time = date("Y-m-d H:i:s",$endTimestamp);
//                    if(!empty($epg->repeat_date_time)){
//                        $repeat_date_timestamp = strtotime($epg->repeat_date_time);
//                        $repeat_date_timestamp = ($sign == "+")? $repeat_date_timestamp+($timeZoneHour*60*60) : $repeat_date_timestamp-($timeZoneHour*60*60);
//                        $repeat_date_timestamp = ($sign == "+")? $repeat_date_timestamp+($timeZoneMinute*60)  : $repeat_date_timestamp-($timeZoneMinute*60);
//                        $epgs[$i]->repeat_date_time = date("Y-m-d H:i:s",$repeat_date_timestamp);
//                    }
//
//                    $epgs[$i]->program_logo = (!empty($epg->program_logo))? $apiSettings->default_image_path.$epg->program_logo : $apiSettings->default_image_path.$organizationInfo->default_epg_logo;
//
//                }
//            }
//
//            if(empty($epgs[0]->id))
//            {
//                array_shift($epgs);
//            }
//            $response = new Response($this->request->apiName);
//            $response->setResponse(array(
//                'code' => 200,
//                'notification' => true,
//                'notificationType' => 1,
//                'ads' => true,
//                'adsType' => 1,
//                'epgs' => $epgs
//            ));
//
//            return AES_Engine::getEncrypt($response, Config::get('app.encryption_key'));
//        }catch(\Exception $ex){
//            $response = new Response();
//            $response->setStatus(1);
//            $response->setDebugCode(100);
//            $response->setDebugMsg($ex->getMessage().', Line:'.$ex->getLine());
//            return AES_Engine::getEncrypt($response,Config::get('app.encryption_key'));
//        }
        
        $programs = [];
	date_default_timezone_set('Asia/Dhaka');
	$time = time();
	$seconds = date("i",time());
        
	$start_time = strtotime(date('Y-m-d H:i',time()));//mktime(date('h',time()),date('i',time()),date('s',time()),date('m',time()),date('d',time()),date('Y',time()));
	$end_time = null;
	$duration = 30;



	for($i=1; $i<=48; $i++){
		$rand = rand(1,5);
                if($seconds> 0 && $seconds < 30){
                    $start_time = $start_time-$seconds;
                }
		$end_time = $start_time+(30*60);
		$program = explode("#",$this->program_name($i));
		$repeat_times = ($rand == 4 || $rand == 5)? null : array(
				'9:00 PM | Friday | 01.03.17',
				'9:00 PM | Sunday | 02.03.17',
				'9:00 PM | Monday | 03.03.17'
			);

		$description = ($rand == 3 || $rand == 5)? null : 'এটিএন বাংলায় আজ (২৮ এপ্রিল) রাত ৮টায় প্রচারিত হবে নতুন ধারাবাহিক নাটক ‘মুখোশ’। ধারাবাহিকটি রচনা করেছেন আহমেদ শাহাবুদ্দীন এবং পরিচালনায় রয়েছেন শাহাদাৎ হোসেন সুজন। বিভিন্ন চরিত্রে অভিনয় করেছেন তারিক আনাম খান, আল মামুন, সাবেরী আলম, শহীজ্জামান সেলিম, জয়শ্রী কর জয়া, মুবিনা আহমেদ কাজল, হিল্লোল, নওশীন, হাসান ফেরদৌস জুয়েল, নিলয়, শখ, মুকুল সিরাজ, রাজিব সালেহীন, রাজীব রাজ, স্নেহা, রিপন, রীতু, নয়ন, মোস্তফা প্রমুখ। নাটকের কাহিনী গড়ে উঠেছে এক শিল্পপতির পরিবারকে কেন্দ্র করে। ধনাঢ্য ব্যবসায়ী আসিফ খান ও জহির খান উত্তরাধিকার সূত্রে বাবার বিশাল কোম্পানির মালিক। বড় ভাই আসিফ বাবার আদর্শে নিজেকে তৈরি করলেও ছোট ভাই জহির ঠিক তার বিপরীত এবং আদর্শহীন। ব্যবসায় এবং পরিবারে একটা শীতল দ্বন্দ্ব চলে দুই ভাইয়ের মধ্যে। এই দ্বন্দ্বের সুযোগে তাদের মাঝখানে আবির্ভাব ঘটে রহস্যজনক ব্যবসায়ী আমজাদ খোরাসানির। তারপর থেকেই দুই ভাইয়ের ব্যবসায় একের পর এক দুর্ঘটনা ঘটতে থাকে। কে ঘটাচ্ছে এই ঘটনা? আমজান খোরাসানি? নাকি অন্য কেউ? ঘটনার রহস্য অনুসন্ধান করতে গিয়ে এক এক করে চেনা মানুষগুলোর মুখোশ উন্মোচিত হতে থাকে।';
        
                date_default_timezone_set("Asia/Dhaka");
		$programs[] = array(
			'program_id' => $i,
			'program_name' => $program[0],
			'program_description' => $description,
			'program_logo'		  => trim($program[1]),
			'duration'		  => $duration,
			'start_time'		  => date("Y-m-d h:i a",$start_time),
			'end_time'                =>  date("Y-m-d h:i a",$end_time),
			'repeat_times'            => $repeat_times,
			'expandable' => ($rand == 5)? false : true
		  );
		$start_time = $end_time; 	

	}

	

	//header("Content-Type: application/json");

	$default_program = array_shift($programs);
	unset($default_program['repeat_times']);
	unset($default_program['expandable']);
//	unset($default_program['start_time']);
//	unset($default_program['end_time']);
	unset($default_program['id']);
        
	$response = new Response($this->request->apiName);
        $response->setResponse([
		'default'  => $default_program,
		'programs' => $programs
	]);
	return AES_Engine::getEncrypt($response, Config::get('app.encryption_key'));

    }
    
    public function getNewEpgs()
    { 
       
        //$default_program = Epg::select("id","program_id","program_name","program_description","program_logo","duration","show_date",DB::raw("concat(show_date,' ',start_time) as start_time"),DB::raw("concat(show_date,' ',end_time) as end_time"))
        //                    ->where("show_date",">=",date('Y-m-d'))->where('start_time','>=',date('H:i:s'))->first();
        $programs        = Epg::select("id","program_id","program_name","program_description","program_logo","duration","show_date",DB::raw("concat(show_date,' ',start_time) as start_time"),DB::raw("concat(show_date,' ',end_time) as end_time"))
                            ->where("show_date",">=",date('Y-m-d'))
                            ->where("program_id",$this->request->contentId)
                            ->orderBy('show_date','asc')->get();
                            
        $appSetting = ApiSetting::find(1);
        
        if(!empty($programs)){
            $default_program = null;
            foreach($programs as $i=> $program){
                $repeat_programs = Epg::select(DB::raw("concat(show_date,' ',start_time) as start_time"))
                        ->where("program_name",$program->program_name)
                        ->where("show_date",$program->show_date)
                        ->where('id','!=',$program->id)
                        ->get();
                $programs[$i]->repeat_times = null;
                if(!empty($repeat_programs)){
                    $arr = null;
                    foreach($repeat_programs as $rp){
                        $arr[] = $rp->start_time;
                    }
                    $programs[$i]->repeat_times = $arr;
                }
                
                if(!empty($program->program_logo)){
                    $programs[$i]->program_logo = $appSetting->default_image_path.$program->program_logo;
                }
                
                $programs[$i]->expandable = true;
                if(empty($repeat_programs) && empty($program->program_description)){
                    $programs[$i]->expandable = false;
                }
                
                $programStartDateTimestamp = strtotime($program->start_time);
                date_default_timezone_set('Asia/Dhaka');
                $nowTimestamp = time();
                $timeDiffInSec = ($nowTimestamp-$programStartDateTimestamp);
                if($timeDiffInSec >= 0 && $timeDiffInSec <=(60*60*30)){
                    $default_program = $program;
                }
            }
        }
        $response = new Response($this->request->apiName);
        $response->setResponse([
            'default' => $default_program,
            'programs' => (count($programs))? $programs : null
        ]);
        return AES_Engine::getEncrypt($response, Config::get('app.encryption_key'));
    }
    
    private function program_name($i)
    {
            $fs = file_get_contents('/var/www/html/api/public/channel.txt');
            $program_names = explode("\n",$fs);
            return $program_names[$i];
    }
}