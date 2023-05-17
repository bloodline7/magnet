<?php

namespace Ausumsports\Admin\Http;

use App\Http\Controllers\Controller;

use Ausumsports\Admin\Models\Admin;
use Ausumsports\Admin\Models\CodeGroup;
use Ausumsports\Admin\Models\Code;
use Ausumsports\Admin\Models\ConfigData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class System extends Controller
{

    private static  $config = null;

    function index()
    {
        return $this->adminList();
    }

    function adminList()
    {
        $data = Admin::orderBy('id', 'desc')->get();
        return View("adminViews::system/adminList", ['data' => $data ,'sub'=>'system']);
    }

    function store()
    {
        return View("adminViews::system/store", ['sub'=>'system']);
    }

    function template()
    {
        return View("adminViews::system/template", ['sub'=>'system']);
    }

    function smtp()
    {
        return View("adminViews::system/smtp", ['sub'=>'system']);
    }


    function cropper()
    {
        return View("adminViews::popup/cropper");
    }




    function saveSetting(Request $request)
    {
        $Data = $request->post();
        foreach ($Data as $Key => $value)
        {
            $Result = ConfigData::updateOrCreate(['path' => $Key ], ['value' => $value]);
        }
        return Ok("Save Complete");
    }


    static function getConfig($Key, $cache=true)
    {
        if(!$Key) return '';

        if(!self::$config)
        {
            if($cache)
                $Result = ConfigData::get();
            else
                $Result = ConfigData::disableCache()->get();

            foreach ($Result as $item)
            {
                self::$config[$item->path] = $item->value;
            }
        }

        return self::$config[$Key]??'';
    }



    function codeManager($id=null)
    {

        $codeGroup = $this->codeGroupList();

        if($id)
        {
            $Result = Code::where('group_id', $id);
        }
        else
        {
            $Result = new Code();
        }


        $data = $Result->orderBy('group_id', 'ASC')->orderBy('sort_no', 'ASC')->get();
        return View("adminViews::system/codeList", ['data' => $data , 'group' => $codeGroup ,'sub'=>'system']);
    }


    function getFile($filename)
    {
        try
        {
            $path = base_path()."/packages/Ausumsports/Admin/resources/data/".$filename;
            return File::get($path);
        }
        catch (Illuminate\Contracts\Filesystem\FileNotFoundException $exception)
        {
            die("The file doesn't exist");
        }
    }

    function timezone($value='Asia/Seoul'): string
    {
        $timezone = json_decode($this->getFile("time_zone.json"));
        $code = '';
        foreach ($timezone as $item)
        {
            $checked = ($item->code == $value) ? " selected " : '';
            $code .= '<option value="'.$item->code.'"' . $checked .'> ' . $item->code .'</option>'."\n";
        }
        return $code;
    }


    function states($value=''): string
    {
        $states = json_decode($this->getFile("usa_states.json"));
        $code = '';

        foreach ($states as $key => $item)
        {
            $checked = ($key == $value) ? " selected " : '';
            $code .= '<option value="'.$key.'"' . $checked .'> ' . $item .'</option>'."\n";
        }

        return $code;
    }

    function number_option($start, $end, $append='', $value=''): string
    {
        $code = '';

        for($i = $start; $i <= $end; $i++)
        {
            $checked = ($i == $value) ? " selected " : '';

            $item = ($append) ? $i .' '.$append : $i;
            $item .= ($append && ($i > 1)) ? 's' : '';
            $code .= '<option value="'.$i.'"' . $checked .'> ' . $item .'</option>'."\n";
        }


        return $code;
    }

    function convention($value)
    {

        $Convention = new \Ausumsports\Admin\Models\Convention();
        $Result = $Convention->orderby('id')->get();

        $code = '';

        foreach ($Result as $item)
        {
            $checked = ($item->id == $value) ? " selected " : "";
            $code .= '<option value="'.$item->id.'"' . $checked .'> ' . $item->title .'</option>';
        }

        return $code;

    }


    function code($groupName, $value=null): string
    {

        if($groupName == "timezone")
                return $this->timezone($value);

        if($groupName == "states")
            return $this->states($value);


        if($groupName == "convention")
            return $this->convention($value);



        if($groupName == "hour")
            return $this->number_option(1, 12, 'Hour', $value);




        $codeGroup = CodeGroup::where("title", $groupName)->first();

            if($codeGroup->id)
            {
                $result = Code::where('group_id', '=', $codeGroup->id)->orderby('sort_no')->get();

                $code = '';
                $checked = '';

                foreach ($result as $item)
                {
                    if(is_array($value))
                    {
                        if(in_array($item->id, $value))
                            $checked = " selected ";
                        else
                            $checked = "";
                    }
                    else {

                        $checked = ($item->id == $value) ? " selected " : "";
                    }


                    $code .= '<option value="'.$item->id.'"' . $checked .'> ' . $item->title .'</option>';
                }

            }
            else {

                return "Code not found";
            }

            return $code ?? '';
    }

    public function imageUploadPost(Request $request)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $path = Storage::disk('s3')->put('uploads', $request->file, 'public' );

        $url = Storage::disk('s3')->url($path);

        return ok("Image Save Success", ['url' => $url]);
    }

    function codeCreate(Request $request)
    {

        $code = new Code();

        $code->title = $request->title;
        $code->group_id = $request->group_id;
        $code->sort_no = $request->sort_no;

        $code->save();
        return ok("Code Created");
    }

    function codeUpdate($id, Request $request)
    {
        $code = Code::find($id);

        $code->group_id = $request->group_id;
        $code->sort_no = $request->sort_no;
        $code->title = $request->title;

        $code->save();

        return ok("Code Update Success");
    }

    function codeDelete($id)
    {
        $code = Code::find($id);
        $code->delete();
        return ok("Code Delete Success");
    }

    function codeGroup()
    {
        $data = CodeGroup::orderBy('id', 'desc')->get();
        return View("adminViews::system/codeGroup", ['data' => $data ]);
    }


    function codeGroupSave(Request $request): \Illuminate\Http\JsonResponse
    {

        $codeGroup = new CodeGroup();

        $codeGroup->title = $request->title;
        $codeGroup->save();

        return ok("Code Group Saved");

    }

    function codeGroupUpdate($id, Request $request): \Illuminate\Http\JsonResponse
    {

        $codeGroup = CodeGroup::find($id);

        $codeGroup->title = $request->title;
        $codeGroup->save();

        return ok("Code Group Updated");
    }




    function codeGroupDelete($id): \Illuminate\Http\JsonResponse
    {
           $codeGroup = CodeGroup::find($id);
           $codeGroup->delete();

        return ok("Code Group Deleted");
    }





    function codeGroupList()
    {
        $data = CodeGroup::orderBy('id', 'desc')->get();

        $newCollection= $data->mapWithKeys(function ($item) {
            return [$item['id'] => $item['title']];
        });

       // $newCollection->prepend(['0' => "Select Group" ]);

        return $newCollection;
    }



}
