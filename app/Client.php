<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Client extends Model
{
	  use SoftDeletes;
	  public $timestamps = false;
      protected $fillable = [
        'id','name','sub_account' ,'logo','vacant_posts', 'contact', 'email','parent_id','description','bulk_tcat_id','region_id','zone_id','branch_id','total_employees','sort_order','focal_person'
       ];

  public function designations(){
        return $this->hasMany('App\Designation','department_id');
    }

    public function users(){
        return $this->hasMany('App\User','department_id');
    }

    public function employees(){
        return $this->hasMany('App\User','department_id')->whereIn('role_id',[6,7]);
    }

    public function roaster(){
        return $this->hasMany('App\Roaster','department_id');
    }

    public function TimeCategory()
    {
        return $this->hasOne(TimeCategory::class,'id','bulk_tcat_id');
    }

    public function parent(){
        return $this->belongsTo(Client::class,'parent_id');
    }

    public static function children($parent_id){
        return Client::where('parent_id', $parent_id)->orderBy('sort_order', 'ASC')
        ->orderBy('name', 'ASC')->get();
    }
    public static function generateRegionalDepartmentsTree($dmg_check,$dmg_id,$includeAll=false, $choose=false)
    {
        if($includeAll)
            $tree_departments = [['id'=>'all','text'=>'All Departments']];
        elseif($choose)
            $tree_departments = [['id'=>'choose one','text'=>'Choose Department']];
        else
            $tree_departments = [];

         $clients = self::where($dmg_check,$dmg_id)->orderBy('sort_order', 'ASC')->orderBy('name', 'ASC')->get()->toArray();
     

        // Specify the root ID to start building the tree
        $parents=[];
        $process_ids=[];

        foreach($clients as $dep2)
        {
            $dep = ['id'=>$dep2['id'],'name'=>$dep2['name'],'parent_id'=>$dep2['parent_id']];

            //echo '<br>NewDep:';
            //print_r($dep);
            
            //echo '<br>parents before op:';
           // print_r($parents);
            
            if(in_array($dep['parent_id'],$process_ids))
            {
                //echo '<br>Add on Spot';

                if(array_key_exists($dep['id'],$parents)){
                    //echo '<br>parents before op:';
                    //print_r($parents);

                    $maindex = $parents[$dep['id']]['inc'];
                    unset($parents[$dep['id']]);
                    
                    //echo '<br>parents After op:';
                    //print_r($parents);
                    
                    $dep['inc']=$maindex;

                    //echo '<br>child:';
                    //print_r($dep);
                }

                
                $parents = self::addChild($parents,$dep);
            }
            else 
            {
                //echo '<br>Add in parents';
                if(array_key_exists($dep['id'],$parents)){

                    $maindex = $parents[$dep['id']]['inc'];
                    unset($parents[$dep['id']]);

                    $parents[$dep['parent_id']]['inc'][$dep['id']] = ['id'=>$dep['id'],'text'=>$dep['name'],'inc' => $maindex];
                    
                }
                else
                {
                    $parents[$dep['parent_id']]['inc'][$dep['id']]= ['id'=>$dep['id'],'text'=>$dep['name']];
                    
                }
            }
            $process_ids[]=$dep['id'];
            
            //echo '<br>parents after op:';
            //print_r($parents);
        }

        //echo 'Output: <pre>';
        //print_r($parents);
        //exit;

        $newp=[];
        foreach($parents as $value)
        {
            if(isset($value['id']))
            {
                $newp[]=['id'=>$value['id'],'text'=>$value['text'],'inc' => self::processprents($value['inc'])];
            }
            else
            {
                $newp[]=self::processprents($value['inc']);
            }       
        }
        
        //echo 'Output: <pre>';
       // print_r($newp);
        //exit;

        foreach($newp as $key=>$value)
        {
            foreach($value as $arr)
                $tree_departments[]=$arr;
        }
  
        return $tree_departments;
    }
    public static function findChild($parents, $dep_id)
    {
        foreach($parents as $k => $value){
            if ($k === $dep_id) {
                $maindex = $parents[$k]['inc'];
                unset($parents[$k]);

                return ['parents'=>$parents,'childs'=>$maindex];
            }
            elseif(isset($value['inc']) && count($value['inc'])>0)
            {
                $output1 = self::findChild($value['inc'], $dep_id);
                if($output1!='no-update')
                {
                    $parents[$k]['inc'] = $output1['parents'];

                    $output2 = ['parents'=>$parents,'childs'=>$output1['childs']];
                    return $output2;
                }
            }
        }
        return 'no-update';
    }

    public static function addChild($parents, $dep, $level = 0)
    {

        foreach($parents as $k => $value) {
           // echo '<br>positioning:'.$dep['id'].'-under-'.$dep['parent_id'].'-currently proceccing-'.$k.'-onlevel-'.$level.'=>';

            if ($k === $dep['parent_id']) { 
                
                if(isset($dep['inc']))
                {
                    $parents[$k]['inc'][$dep['id']]= ['id'=>$dep['id'],'text'=>$dep['name'],'inc'=>$dep['inc']];
                }else{
                    $parents[$k]['inc'][$dep['id']]= ['id'=>$dep['id'],'text'=>$dep['name']];
                }

                return $parents;
            }else{ 
                if(isset($value['inc']) && count($value['inc'])>0)
                {
                    $output = self::addChild($value['inc'], $dep,$level+1);
                    if($output!='no-update')
                    {
                        $parents[$k]['inc'] = $output;
                        return $parents;
                    }
                        
                }
            }
        }
        return 'no-update';
    }
    public static function processprents($array,$level = 0)
    {
        $outputArray = [];
        if(isset($array['id']))
        {
        	if(isset($array['inc']))
        	{
                //echo 'test for id and child';
        		$outputArray = ['id'=>$array['id'],'text'=>$array['text'],'inc'=>self::processprents($array['inc'])];
        	}
        	else
        	{
                //echo 'test for id';
        		$outputArray = ['id'=>$array['id'],'text'=>$array['text']];
        	}
        }elseif(isset($array['inc'])){
            //echo 'test for child';
        	$outputArray=self::processprents($array['inc']);       
        }
        else{
        	foreach ($array as $value) {
	            if (isset($value['inc'])) {//"id":2751,"text":"Secretariats","inc"
	                $outputArray[] = ['id'=>$value['id'],'text'=>$value['text'],'inc'=>self::processprents($value['inc'])];
	            }else{

	                $outputArray[] = ['id'=>$value['id'],'text'=>$value['text']];
	            }
	        }
        }
        return $outputArray;
    }


    public static function generatetree($parent=0,$includeSelf=false,$includeAll=false, $choose=false)
    {

        if($includeAll)
            $tree_departments = [['id'=>'all','text'=>'All Departments']];
        elseif($choose)
            $tree_departments = [['id'=>'choose one','text'=>'Choose Department']];
        else
            $tree_departments = [];

        if($includeSelf)
        {
            $self = self::where('id',$parent)->first();
            $tree_departments[] = [
                'id' => $self->id,
                'text' => $self->name,
                'inc' => self::generatetree($self->id)
                ];
        }
        else
        {
            $clients = self::where('parent_id',$parent)->orderBy('sort_order', 'ASC')->orderBy('name', 'ASC')->get();
            foreach($clients as $client){

                $tree_departments[] = [
                'id' => $client->id,
                'text' => $client->name,
                'inc' => self::generatetree($client->id)
                ];

            }
        }

        return $tree_departments;
    }

    public static function getSubDepartments($parent=0)
    {

        $tree_departments[] = $parent;
        $clients = self::where('parent_id',$parent)->pluck('id');

        foreach($clients as $client){
            $tree_departments[] = $client;
            $tree_departments = array_merge($tree_departments,self::getSubDepartments($client));


        }
        return $tree_departments;
    }

}
