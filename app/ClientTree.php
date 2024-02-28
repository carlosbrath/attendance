<?php

namespace App;
use App\Client;

use Illuminate\Database\Eloquent\Model;

class ClientTree extends Model
{
    //
    protected $table='client_trees';
    protected $fillable = [
        'id', 'children_ids','all_children_ids', 'child_label_json','tree_jason'
       ];

       public static function generatetree($dep_id=0)
       {
            $tree_departments = [];
            $child_ids = [];
            $child_labels = [];

            $clients = Client::where('parent_id',$dep_id)->orderBy('sort_order', 'ASC')->orderBy('name', 'ASC')->get();
            foreach($clients as $client){
                $tree_departments[] = ['id' => $client->id,'text' => $client->name,'inc' => self::generatetree($client->id)];
                $child_ids[] = $client->id;
                $child_labels[] = ['id' => $client->id,'text' => $client->name];
            }

            $dep = self::find($dep_id);
            if(!$dep){
                $dep = new ClientTree();
                $dep->id = $dep_id;
            }
            $dep->tree_jason = json_encode($tree_departments);
            $dep->children_ids =  implode(',',$child_ids);
            $dep->child_label_json = json_encode($child_labels);

            $dep->save();

            return $tree_departments;
       }
       public static function generate_all_child_ids($dep_id=0){            
            $tree_departments = [];

            $clients = Client::where('parent_id',$dep_id)->pluck('id')->toArray();
            foreach($clients as $client){
                $tree_departments = array_merge($tree_departments,self::generate_all_child_ids($client));
                array_push($tree_departments, $client);
            }

            $dep = self::find($dep_id);
            if(!$dep){
                $dep = new ClientTree();
                $dep->id = $dep_id;
            }
            $dep->all_children_ids = implode(',',$tree_departments);
            
            $dep->save();


            return $tree_departments;
        }

}
