<?php
namespace App\Entities\Contents;
/**
 * Description of ContentManager
 *
 * @author Himel
 */
use Illuminate\Support\Facades\DB;
use App\Entities\Program;
use stdClass;


class ContentManager {
    //put your code here
    const FEATURE_ITEM_LIMIT = 15;
    const PARENT_ID = 1;
    
    public static function getContents($request,$userId,$deviceType,$appSetting)
    {
        $programs = DB::table('iptv_programs');

            if(!empty($request->subCategoryId)) {

                $programs = $programs->join('iptv_category_programs', 'iptv_category_programs.program_id', '=', 'iptv_programs.id');
            }

            $selectFilter = array(
                'iptv_programs.id','iptv_programs.program_name','iptv_programs.channel_logo','iptv_programs.video_share_url','iptv_programs.video_trailer_url',
                'iptv_programs.description','iptv_programs.water_mark_url','iptv_programs.type','iptv_programs.view_count',
                'iptv_programs.lcn','iptv_programs.individual_price','iptv_programs.video_tags','iptv_programs.duration',
                'iptv_programs.age_restriction','iptv_programs.is_available','iptv_programs.water_mark_url','iptv_programs.service_operator_id'
            );
            
            self::_setLogoFilter($selectFilter, $deviceType, $appSetting);

            $selectFilter = implode(",",$selectFilter)." , if(HasFavoriteContent(iptv_programs.id,{$userId}),1,0) as favorite";
            
            $programs = $programs->select(DB::raw($selectFilter));

            if($request->type == Program::LIVE){
                $programs = $programs->where('iptv_programs.type','!=',Program::VOD)
                    ->where('iptv_programs.type','!=',Program::CATCHUP);
                

            }else{
                $programs = $programs->where('iptv_programs.type',$request->type);
            }
            $parentId = (!empty($request->parentId))? $request->parentId : self::PARENT_ID;
            $programs = $programs->where('iptv_programs.is_remove',0)
                    ->where('iptv_programs.is_active',1)  
                    ->where('parent_id',$parentId)
                    ->orwhere(function($q)use($request){
                        if($request->type == Program::LIVE){
                            $q->where('iptv_programs.type','!=',Program::VOD)
                                    ->where('iptv_programs.type','!=',Program::CATCHUP)
                                    ->where('lsp_type_id',2);
                        }else{
                            $q->where('iptv_programs.type',$request->type)->where('lsp_type_id',2);
                        }
                    });
                    

            
            
            if(!empty($request->subCategoryId)){

                $programs = $programs->where('iptv_category_programs.sub_category_id',$request->subCategoryId);
            }

            $programs = $programs->groupBy('iptv_programs.id');
            
            if($request->type == Program::LIVE){
                $programs = $programs->orderBy('iptv_programs.lcn','asc');
            }else{
                $programs = $programs->orderBy('iptv_programs.id','desc');
            }

            if(isset($request->limit)){

                $programs = $programs->take($request->limit)
                                     ->skip($request->offset);
            }
            
            return $programs->get();
    }
    
    public static function countContents($request)
    {
        $countSelect = 'select count(iptv_programs.id) as total from iptv_programs';
        if(!empty($request->subCategoryId)) {
            $countSelect .= ' join iptv_category_programs on iptv_category_programs.program_id = iptv_programs.id';
        }
        $parentId = (!empty($request->parentId))? $request->parentId : self::PARENT_ID;
        if($request->type == Program::LIVE){
            $countSelect .= " WHERE is_remove = 0 AND (parent_id = ".$parentId." OR lsp_type_id = 2) AND is_active = 1 AND (iptv_programs.type != '".Program::CATCHUP."' AND iptv_programs.type != '".Program::VOD."')";
        }else{
            $countSelect .= " WHERE is_remove = 0 AND (parent_id = ".$parentId." OR lsp_type_id = 2) AND is_active = 1 AND iptv_programs.type = '{$request->type}'";
        }

        if(!empty($request->subCategoryId)){
            $countSelect .= " AND iptv_category_programs.sub_category_id = ".$request->subCategoryId;
        }

        $total = DB::select($countSelect);
        $total =(!empty($total))? array_shift($total) : null;
        return (!empty($total))? $total->total : 0;
    }
    
    public static function getFeatureContents($request,$userId,$deviceType,$appSetting)
    {
        $programs = DB::table('iptv_programs');

        if(!empty($request->subCategoryId)) {
            $programs = $programs->join('iptv_category_programs', 'iptv_category_programs.program_id', '=', 'iptv_programs.id');
        }

        $selectFilter = array(
            'iptv_programs.id','iptv_programs.program_name','iptv_programs.channel_logo','iptv_programs.video_share_url','iptv_programs.video_trailer_url',
            'iptv_programs.description','iptv_programs.water_mark_url','iptv_programs.type','iptv_programs.view_count',
            'iptv_programs.lcn','iptv_programs.individual_price','iptv_programs.video_tags','iptv_programs.duration',
            'iptv_programs.age_restriction','iptv_programs.is_available','iptv_programs.water_mark_url','iptv_programs.service_operator_id'
        );

        self::_setLogoFilter($selectFilter, $deviceType, $appSetting);
        $selectFilter = implode(",",$selectFilter);
        
        if($userId){
            $selectFilter .= " , if(HasFavoriteContent(iptv_programs.id,{$userId}),1,0) as favorite";
        }
        
        $programs = $programs->select(DB::raw($selectFilter));

        if($request->type == Program::LIVE){
            $programs = $programs->where('iptv_programs.type','!=',Program::VOD)
                ->where('iptv_programs.type','!=',Program::CATCHUP);
        }else{
            if($request->type != "NULL")
                $programs = $programs->where('iptv_programs.type',$request->type);
        }

        $programs = $programs->where('iptv_programs.is_remove',0)
                             ->where('featured',1)
                             ->where('is_active',1);


        if(!empty($request->subCategoryId)){

            $programs = $programs->where('iptv_category_programs.sub_category_id',$request->subCategoryId);
        }

        $programs = $programs->groupBy('iptv_programs.id');
        $programs = $programs->inRandomOrder();

        $programs = $programs->take(self::FEATURE_ITEM_LIMIT);
        return $programs->get();
    }
    
    public static function countFeatureContent($request)
    {
        $countSelect = 'select count(iptv_programs.id) as total from iptv_programs';
        if(!empty($request->subCategoryId)) {
            $countSelect .= ' join iptv_category_programs on iptv_category_programs.program_id = iptv_programs.id';
        }
        if($request->type == Program::LIVE){
            $countSelect .= " WHERE is_remove = 0 AND is_active=1 AND featured=1 AND (iptv_programs.type != '".Program::CATCHUP."' AND iptv_programs.type != '".Program::VOD."')";
        }else{
            if($request->type != "NULL")
                $countSelect .= " WHERE is_remove = 0 AND is_active=1 AND featured=1 AND iptv_programs.type = '{$request->type}'";
            else
                $countSelect .= " WHERE is_remove = 0 AND featured=1 AND is_active=1";
        }

        if(!empty($request->subCategoryId)){
            $countSelect .= " AND iptv_category_programs.sub_category_id = ".$request->subCategoryId;
        }
        $total = DB::select($countSelect);
        $total = (!empty($total))? array_shift($total) : null;
        return  (!empty($total))? $total->total : 0;
    }
    
    public static function getPopularContents($request,$userId,$deviceType,$appSetting)
    {
        $programs = DB::table('iptv_programs');

        if(!empty($request->subCategoryId)) {

            $programs = $programs->join('iptv_category_programs', 'iptv_category_programs.program_id', '=', 'iptv_programs.id');
        }

        $selectFilter = array(
            'iptv_programs.id','iptv_programs.program_name','iptv_programs.channel_logo','iptv_programs.video_share_url','iptv_programs.video_trailer_url',
            'iptv_programs.description','iptv_programs.water_mark_url','iptv_programs.type','iptv_programs.view_count',
            'iptv_programs.lcn','iptv_programs.individual_price','iptv_programs.video_tags','iptv_programs.duration',
            'iptv_programs.age_restriction','iptv_programs.is_available','iptv_programs.water_mark_url','iptv_programs.service_operator_id'
        );

        self::_setLogoFilter($selectFilter, $deviceType, $appSetting);

        $selectFilter = implode(",",$selectFilter)." , if(HasFavoriteContent(iptv_programs.id,{$userId}),1,0) as favorite";
        $programs = $programs->select(DB::raw($selectFilter));


        $programs = $programs->where('iptv_programs.is_remove',0)->where('iptv_programs.is_active',1)
                             ->where('view_count','>','0');

        if(!empty($request->subCategoryId)){

            $programs = $programs->where('iptv_category_programs.sub_category_id',$request->subCategoryId);
        }

        $programs = $programs->groupBy('iptv_programs.id');
        $programs = $programs->orderBy('iptv_programs.view_count','desc');

        if(isset($request->limit)){

            $programs = $programs->take($request->limit)
                ->skip($request->offset);
        }

        return $programs->get();
    }
    
    public static function countPopularContent($request)
    {
        $countSelect = 'select count(iptv_programs.id) as total from iptv_programs';
        if(!empty($request->subCategoryId)) {
            $countSelect .= ' join iptv_category_programs on iptv_category_programs.program_id = iptv_programs.id';
        }


        $countSelect .= " WHERE is_remove = 0 AND is_active = 1 AND view_count > 0";

        if(!empty($request->subCategoryId)){
            $countSelect .= " AND iptv_category_programs.sub_category_id = ".$request->subCategoryId;
        }
        $total = DB::select($countSelect);
        $total = (!empty($total))? array_shift($total) : null;
        return (!empty($total))? $total->total : 0;
    }
    
    public static function getHistoryContents($request,$userId,$deviceType,$appSetting)
    {
        $selectFilter = array(
            'iptv_programs.id','iptv_programs.program_name','iptv_programs.channel_logo','iptv_programs.video_share_url','iptv_programs.video_trailer_url',
            'iptv_programs.description','iptv_programs.type','iptv_programs.view_count',
            'iptv_programs.lcn','iptv_programs.individual_price','iptv_programs.video_tags','iptv_programs.duration',
            'iptv_programs.age_restriction','iptv_programs.is_available','iptv_programs.water_mark_url','iptv_programs.service_operator_id','current_viewers.created_at as last_watch_time'
        );

          self::_setLogoFilter($selectFilter, $deviceType, $appSetting);

        
        
        
        $query = "SELECT * FROM (select ".implode(",",$selectFilter);
        if($userId){
            $query .= ", if(HasFavoriteContent(iptv_programs.id,{$userId}),1,0) as favorite";
        }
        $query .= " from iptv_programs
        join current_viewers 
        ON current_viewers.content_id = iptv_programs.id
        where is_remove = 0 AND is_active = 1 AND current_viewers.customer_id = {$request->customerId}
         order by last_watch_time desc) p
        group by p.id order by p.last_watch_time desc ";
        if(isset($request->limit)){
            $query .= " LIMIT {$request->offset},{$request->limit}";
        }
        $programs = DB::select(DB::raw($query));

        return $programs;
    }
    
    public static function countHistoryContents($request)
    {
        $countSelect = 'SELECT count(id) as total FROM(select  iptv_programs.id from iptv_programs
        join current_viewers on current_viewers.content_id = iptv_programs.id';
        $countSelect .= " WHERE current_viewers.customer_id={$request->customerId} AND is_remove = 0 AND is_active = 1 group by current_viewers.content_id) as p";
        $total = DB::select($countSelect);
        $total = (!empty($total))? array_shift($total) : null;
        return (!empty($total))? $total->total : 0;
    }
    
    public static function getRelativeContents($request,$userId,$deviceType,$appSetting)
    {
        $programs = DB::table('iptv_programs');
        
        $selectFilter = array(
            'iptv_programs.id','iptv_programs.program_name','iptv_programs.channel_logo','iptv_programs.video_share_url','iptv_programs.video_trailer_url',
            'iptv_programs.description','iptv_programs.water_mark_url','iptv_programs.type','iptv_programs.view_count',
            'iptv_programs.lcn','iptv_programs.individual_price','iptv_programs.video_tags','iptv_programs.duration',
            'iptv_programs.age_restriction','iptv_programs.is_available','iptv_programs.water_mark_url','iptv_programs.service_operator_id'
        );

        self::_setLogoFilter($selectFilter, $deviceType, $appSetting);

        $selectFilter = implode(",",$selectFilter)." , if(HasFavoriteContent(iptv_programs.id,{$userId}),1,0) as favorite";
        $programs = $programs->select(DB::raw($selectFilter));



        $programs = $programs->where('iptv_programs.is_remove',0)->where('iptv_programs.is_active',1);
        if(!empty($request->videoTag)){
            $programs = $programs->where('iptv_programs.video_tags',$request->videoTag);
        }


        $programs = $programs->groupBy('iptv_programs.id');
        $programs = $programs->orderBy('iptv_programs.id','desc');

        if(isset($request->limit)){

            $programs = $programs->take($request->limit)
                ->skip($request->offset);
        }

        return $programs->get();
    }
    
    public static function countRelativeContents($request)
    {
        $countSelect = 'select count(iptv_programs.id) as total from iptv_programs';
        if(!empty($request->subCategoryId)) {
            $countSelect .= ' join iptv_category_programs on iptv_category_programs.program_id = iptv_programs.id';
        }
        $countSelect .= " WHERE is_remove = 0 AND is_active = 1";
        if(!empty($request->videoTag)){
            $countSelect .= " AND iptv_programs.video_tags = '".$request->videoTag."'";
        }
        $total = DB::select($countSelect);
        $total = (!empty($total))? array_shift($total) : null;
        return (!empty($total))? $total->total : 0;
    }
    
    public static function getExtRelativeContents($request,$userId,$deviceType,$appSetting,$playingProgram)
    {

        $selectFilter = [];
        self::_setLogoFilter($selectFilter, $deviceType, $appSetting);
        
        $query = "SELECT * FROM ((select 99999999 as tag,iptv_programs.id,iptv_programs.program_name,iptv_programs.channel_logo,iptv_programs.video_share_url,iptv_programs.video_trailer_url,iptv_programs.description,iptv_programs.type,iptv_programs.view_count,iptv_programs.lcn,iptv_programs.individual_price,iptv_programs.video_tags,iptv_programs.duration,iptv_programs.age_restriction,iptv_programs.is_available,iptv_programs.water_mark_url,iptv_programs.service_operator_id,";
        $query .= implode(",",$selectFilter).", if(HasFavoriteContent(iptv_programs.id,{$request->customerId}),1,0) as favorite from `iptv_programs` where `iptv_programs`.`is_remove` = 0 AND iptv_programs.is_active = 1";
        
        if(!empty($request->videoTag)){
            $query .= " and `iptv_programs`.`video_tags` = '{$request->videoTag}'";
        }
        
        $query .= " AND `iptv_programs`.`type` != '".Program::LIVE."' order by `iptv_programs`.`id` desc)";
        
        $query .= "union (select id as tag,iptv_programs.id,iptv_programs.program_name,iptv_programs.channel_logo,iptv_programs.video_share_url,iptv_programs.video_trailer_url,iptv_programs.description,iptv_programs.type,iptv_programs.view_count,iptv_programs.lcn,iptv_programs.individual_price,iptv_programs.video_tags,iptv_programs.duration,iptv_programs.age_restriction,iptv_programs.is_available,iptv_programs.water_mark_url,iptv_programs.service_operator_id,";
        $query .= implode(",",$selectFilter).", if(HasFavoriteContent(iptv_programs.id,{$request->customerId}),1,0) as favorite from `iptv_programs` where `iptv_programs`.`is_remove` = 0 AND iptv_programs.is_active = 1";
        
        if(!empty($request->videoTag)){
            $query .= " and `iptv_programs`.`video_tags` != '{$request->videoTag}'";
        }
        
        $query .= " and `iptv_programs`.`type` != '".Program::LIVE."' order by `iptv_programs`.`id` desc))";
        $query .= " as p ";
        
        if(!empty($playingProgram))
        {
            $query .= "WHERE p.id != {$playingProgram->id}";
        }
        
        if(!empty($request->videoTag)){
            $query .= " ORDER BY tag desc";
        }else{
            $query .= " GROUP BY id ORDER BY id desc";
        }
        
        $query .= " LIMIT {$request->offset},{$request->limit}";
        
        return DB::select($query);
    }
    
    public static function countExtRelativeContents($request,$playingProgram)
    {
        $query = "SELECT count(*) as total FROM ((select 99999999 as tag,iptv_programs.id from `iptv_programs` where `iptv_programs`.`is_remove` = 0 AND iptv_programs.is_active = 1 and `iptv_programs`.`video_tags` = '{$request->videoTag}' order by `iptv_programs`.`id` desc)";
        
        $query .= "union (select id as tag,iptv_programs.id from `iptv_programs` where `iptv_programs`.`is_remove` = 0 AND iptv_programs.is_active = 1";
        if(!empty(trim($request->videoTag))){
            $query .= " and `iptv_programs`.`video_tags` != '{$request->videoTag}'";
        }
        $query .= " and `iptv_programs`.`type` != '".Program::LIVE."' order by `iptv_programs`.`id` desc))";
        $query .= " as p";
        if(!empty($playingProgram)){
            $query .= " WHERE p.id != {$playingProgram->id}";
        }
        if(!empty($request->videoTag)){
            $query .= " ORDER BY tag desc";
        }else{
            $query .= " GROUP BY id ORDER BY id desc";
        }
        
        $total = DB::select($query);
        $total = (!empty($total))? array_shift($total) : null;
        return (!empty($total))? $total->total : 0;
    }
    
    public static function getSearchContents($request,$userId,$deviceType,$appSetting)
    {
        $programs = DB::table('iptv_programs');

        if(!empty($request->subCategoryId)) {

            $programs = $programs->join('iptv_category_programs', 'iptv_category_programs.program_id', '=', 'iptv_programs.id');
        }

        $selectFilter = array(
            'iptv_programs.id','iptv_programs.program_name','iptv_programs.channel_logo','iptv_programs.video_share_url','iptv_programs.video_trailer_url',
            'iptv_programs.description','iptv_programs.water_mark_url','iptv_programs.type','iptv_programs.view_count',
            'iptv_programs.lcn','iptv_programs.individual_price','iptv_programs.video_tags','iptv_programs.duration',
            'iptv_programs.age_restriction','iptv_programs.is_available','iptv_programs.water_mark_url','iptv_programs.service_operator_id'
        );

        self::_setLogoFilter($selectFilter, $deviceType, $appSetting);
        $selectFilter = implode(",",$selectFilter);
        if($userId){
            $selectFilter .=" , if(HasFavoriteContent(iptv_programs.id,{$userId}),1,0) as favorite";
        }
        $programs = $programs->select(DB::raw($selectFilter));

        $programs = $programs->where('iptv_programs.is_remove',0)->where('iptv_programs.is_active',1);

        if(!empty($request->subCategoryId)){

            $programs = $programs->where('iptv_category_programs.sub_category_id',$request->subCategoryId);
        }

        $programs = $programs->where(function($q)use($request){
            $q->where('keywords','like','%'.$request->keyword.'%')
              ->orWhere('program_name','like','%'.$request->keyword.'%')
              ->orWhere('video_tags','like','%'.$request->keyword.'%');
        });
        
        $programs = $programs->where('type','!=',Program::LIVE);

        $programs = $programs->groupBy('iptv_programs.id');
        $programs = $programs->orderBy('iptv_programs.lcn','asc');

        if(isset($request->limit)){

            $programs = $programs->take($request->limit)
                ->skip($request->offset);
        }

        return $programs->get();
    }
    
    public static function countSearchContents($request)
    {
        $countSelect = 'select count(iptv_programs.id) as total from iptv_programs';
        if(!empty($request->subCategoryId)) {
            $countSelect .= ' join iptv_category_programs on iptv_category_programs.program_id = iptv_programs.id';
        }
        $countSelect .= " WHERE  iptv_programs.is_remove=0 AND is_active = 1 AND iptv_programs.keywords LIKE '%".$request->keyword."%'";

        if(!empty($request->subCategoryId)){
            $countSelect .= " AND iptv_category_programs.sub_category_id = ".$request->subCategoryId;
        }
        
        $total = DB::select($countSelect);
        $total = (!empty($total))? array_shift($total) : null;
        return (!empty($total))? $total->total : 0;

    }
    
    public static function getNewlyUploadedContents($request,$userId,$deviceType,$appSetting)
    {
        $programs = DB::table('iptv_programs');
            
        if(!empty($request->subCategoryId)) {

            $programs = $programs->join('iptv_category_programs', 'iptv_category_programs.program_id', '=', 'iptv_programs.id');
        }
        $selectFilter = array(
            'iptv_programs.id','iptv_programs.program_name','iptv_programs.channel_logo','iptv_programs.video_share_url','iptv_programs.video_trailer_url',
            'iptv_programs.description','iptv_programs.water_mark_url','iptv_programs.type','iptv_programs.view_count',
            'iptv_programs.lcn','iptv_programs.individual_price','iptv_programs.video_tags','iptv_programs.duration',
            'iptv_programs.age_restriction','iptv_programs.is_available','iptv_programs.water_mark_url','iptv_programs.service_operator_id'
        );

        self::_setLogoFilter($selectFilter, $deviceType, $appSetting);

        $selectFilter = implode(",",$selectFilter)." , if(HasFavoriteContent(iptv_programs.id,{$userId}),1,0) as favorite";
        $programs = $programs->select(DB::raw($selectFilter));

        $programs = $programs->where('iptv_programs.is_remove',0)->where('iptv_programs.is_active',1)
                ->where('iptv_programs.type','!=','LIVE');

        if(!empty($request->subCategoryId)){

            $programs = $programs->where('iptv_category_programs.sub_category_id',$request->subCategoryId);
        }

        $programs = $programs->groupBy('iptv_programs.id');
        $programs = $programs->orderBy('iptv_programs.id','desc');

        if(isset($request->limit)){

            $programs = $programs->take($request->limit)
                ->skip($request->offset);
        }
        return $programs->get();
    }
    
    public static function countNewlyUploadedContents($request)
    {
        $countSelect = 'select count(iptv_programs.id) as total from iptv_programs';
        if(!empty($request->subCategoryId)) {
            $countSelect .= ' join iptv_category_programs on iptv_category_programs.program_id = iptv_programs.id';
        }

        $countSelect .= " WHERE is_remove = 0 AND is_active = 1";

        if(!empty($request->subCategoryId)){
            $countSelect .= " AND iptv_category_programs.sub_category_id = ".$request->subCategoryId;
        }
        
        $total = DB::select($countSelect);
        $total = (!empty($total))? array_shift($total) : null;
        return (!empty($total))? $total->total : 0;
        
    }
    
    public static function getFavoriteContents($request,$userId,$deviceType,$appSetting)
    {
        $programs = DB::table('iptv_programs')
                        ->join('iptv_favorite_programs','iptv_favorite_programs.content_id','=','iptv_programs.id');

        $selectFilter = array(
            'iptv_programs.id','iptv_programs.program_name','iptv_programs.channel_logo','iptv_programs.video_share_url','iptv_programs.video_trailer_url',
            'iptv_programs.description','iptv_programs.water_mark_url','iptv_programs.type','iptv_programs.view_count',
            'iptv_programs.lcn','iptv_programs.individual_price','iptv_programs.video_tags','iptv_programs.duration',
            'iptv_programs.age_restriction','iptv_programs.is_available','iptv_programs.water_mark_url','iptv_programs.service_operator_id'
        );

        self::_setLogoFilter($selectFilter, $deviceType, $appSetting);
       
        $selectFilter = implode(",",$selectFilter);
        if($userId){
            $selectFilter .= " , if(HasFavoriteContent(iptv_programs.id,{$userId}),1,0) as favorite";
        }
        $programs = $programs->select(DB::raw($selectFilter));

        $programs = $programs->where('iptv_programs.is_remove',0)->where('iptv_programs.is_active',1)
                             ->where('iptv_favorite_programs.customer_id',$userId);

        $programs = $programs->groupBy('iptv_programs.id');
        $programs = $programs->orderBy('iptv_favorite_programs.id','desc');

        if(isset($request->limit)){

            $programs = $programs->take($request->limit)
                                 ->skip($request->offset);
        }

        return $programs->get();
    }
    
    public static function countFavoriteContents($userId)
    {
        $countSelect = 'SELECT COUNT(*) as total FROM (select iptv_favorite_programs.content_id as total from iptv_programs';

        $countSelect .= ' join iptv_favorite_programs on iptv_favorite_programs.content_id = iptv_programs.id';

        $countSelect .= " WHERE is_remove = 0 AND is_active = 1 AND iptv_favorite_programs.customer_id =".$userId." group by iptv_favorite_programs.content_id) p";
        $total = DB::select($countSelect);
        $total = (!empty($total))? array_shift($total) : null;
        return (!empty($total))? $total->total : 0;
    }
    
    private static function _setLogoFilter(&$selectFilter,$deviceType,$appSetting)
    {
        if(!empty($deviceType) && $deviceType->isMobile()){

            $selectFilter[]=DB::raw("concat('".$appSetting->default_image_path."',iptv_programs.logo_mobile_url) as logo_mobile_url");
            $selectFilter[]=DB::raw("concat('".$appSetting->default_image_path."',iptv_programs.poster_url_mobile) as poster_url_mobile");

        }else if(!empty($deviceType) && $deviceType->isWeb()){
            $selectFilter[] = DB::raw("concat('".$appSetting->default_image_path."',iptv_programs.logo_web_url) as logo_web_url");
            $selectFilter[] = DB::raw("concat('".$appSetting->default_image_path."',iptv_programs.poster_url_web) as poster_url_web");
            
        }else if(!empty($deviceType) && $deviceType->isSTB()){

            $selectFilter[] = DB::raw("concat('".$appSetting->default_image_path."',iptv_programs.logo_stb_url) as logo_stb_url");
            $selectFilter[] = DB::raw("concat('".$appSetting->default_image_path."',iptv_programs.poster_url_stb) as poster_url_stb");

        }
        
    }
    
    public static function getDefaultHlsLinks($deviceType,$organization)
    {
        $obj = new stdClass();
        if(!empty($deviceType) && $deviceType->isMobile()){
            $obj->hls_url_mobile = $organization->default_hls_mobile;
        }
        if(!empty($deviceType) && $deviceType->isWeb()){
            $obj->hls_url_web = $organization->default_hls_web;
        }
        if(!empty($deviceType) && $deviceType->isSTB()){
            $obj->hls_url_stb = $organization->default_hls_stb;
        }
        
        return array($obj);
    }
    
    public static function getExpiredHlsLinks($deviceType,$organization)
    {
        $obj = new stdClass();
        if(!empty($deviceType) && $deviceType->isMobile()){
            $obj->hls_url_mobile = $organization->default_expire_hls_mobile;
        }
        if(!empty($deviceType) && $deviceType->isWeb()){
            $obj->hls_url_web = $organization->default_expire_hls_web;
        }
        if(!empty($deviceType) && $deviceType->isSTB()){
            $obj->hls_url_stb = $organization->default_expire_hls_stb;
        }
        return array($obj);
    }
    
    public static function getUnsubscribedHlsLinks($deviceType,$organization)
    {
        $obj = new stdClass();
        if(!empty($deviceType) && $deviceType->isMobile()){
            $obj->hls_url_mobile = $organization->default_unsubscribed_hls;
        }
        if(!empty($deviceType) && $deviceType->isWeb()){
            $obj->hls_url_web = $organization->default_unsubscribed_hls;
        }
        if(!empty($deviceType) && $deviceType->isSTB()){
            $obj->hls_url_stb = $organization->default_unsubscribed_hls;
        }
        return array($obj);
    }
    
    public static function getHlsFilter($deviceType)
    {
        $selectHlsFilter = array();
        if(!empty($deviceType) && $deviceType->isMobile()){
            $selectHlsFilter[]="hls_url_mobile";
        }else if(!empty($deviceType) && $deviceType->isWeb()){
            $selectHlsFilter[]="hls_url_web";
        }else if(!empty($deviceType) && $deviceType->isSTB()){
            $selectHlsFilter[]="hls_url_stb";
        }
        return $selectHlsFilter;
    }
    
    public static function setVideoShareLink(&$programs,$program,$appSetting,$organization)
    {
        
        if(Program::isLive($program)){
            $programs->video_share_url = (empty($program->video_share_url))? $organization->default_channel_share_url :
                    $appSetting->default_share_url.$program->video_share_url;
        }
        if(Program::isVod($program)){
            $programs->video_share_url = (empty($program->video_share_url))? $organization->default_vod_share_url :
                    $appSetting->default_share_url.$program->video_share_url;
        }
        if(Program::isCatchup($program)){
            $programs->video_share_url = (empty($program->video_share_url))? $organization->default_catchup_share_url :
                    $appSetting->default_share_url.$program->video_share_url;
        }
             
    }
    
    public static function setDefaultLogoAndPoster(&$programs,$program,$deviceType,$appSetting,$organization)
    {
        if(Program::isLive($program)){
            if (!empty($program->channel_logo)) {
                $programs->channel_logo = $appSetting->default_image_path.$program->channel_logo;
            }
        }
        if(!empty($deviceType) && $deviceType->isMobile()){
            if(empty($program->logo_mobile_url) || !preg_match('/.png/',$program->logo_mobile_url)){
                $programs->logo_mobile_url =  $appSetting->default_image_path.$organization->default_mobile_logo;
            }
            if(empty($program->poster_url_mobile) || !preg_match('/.png/',$program->poster_url_mobile)){
                $programs->poster_url_mobile =  $appSetting->default_image_path.$organization->default_poster_mobile;
            }

        }

        if(!empty($deviceType) && $deviceType->isWeb()){
            if(empty($program->logo_web_url) || !preg_match('/.png/',$program->logo_web_url)){
                $programs->logo_web_url =  $appSetting->default_image_path.$organization->default_web_logo;
            }
            if(empty($program->poster_url_web) || !preg_match('/.png/',$program->poster_url_web)){
                $programs->poster_url_web =  $appSetting->default_image_path.$organization->default_poster_web;
            }
        }

        if(!empty($deviceType) && $deviceType->isSTB()){
            if(empty($program->logo_stb_url) || !preg_match('/.png/',$program->logo_stb_url)){
                $programs->logo_stb_url =  $appSetting->default_image_path.$organization->default_stb_logo;
            }
            if(empty($program->poster_url_stb) || !preg_match('/.png/',$program->poster_url_stb)){
                $programs->poster_url_stb =  $appSetting->default_image_path.$organization->default_poster_stb;
            }
        }
    }
}
