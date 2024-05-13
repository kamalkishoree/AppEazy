<?php

namespace App\Http\Controllers;

use App\Model\FormAttribute;
use App\Model\FormAttributeOption;
use App\Model\FormAttributeOptionTranslation;
use App\Model\FormAttributeTranslation;
use Illuminate\Http\Request;
use App\Traits\FormAttributeTrait;
class FormAttributeController extends Controller
{
    use FormAttributeTrait;
    public function create(Request $request)
    {
        $for =  $request->has('for') ?  $request->for : 1; // for 1 = driver attribute , 2= for driver rating
        $attribute_id =  $request->has('attribute_id') ?  $request->attribute_id :0;
        $attribute = [];
        $returnHTML = view('attributes.add-attribute')->with(['for'=>$for])->render();

        if($attribute_id !=0){
            $attribute = $this->getAttributeForm($request,$attribute_id);
            $returnHTML = view('attributes.edit-attribute')->with(['for'=>$for,'attribute'=>$attribute])->render();
        }

        return response()->json(array('success' => true, 'html' => $returnHTML));
    }

    public function store(Request $request)
    {


        $variant = FormAttribute::where('id',@$request->attribute_id)->first() ?? new FormAttribute();
        $variant->title = (!empty($request->title[0])) ? $request->title[0] : '';
        $variant->type = $request->type;
        $variant->attribute_for = $request->attribute_for ?? 1;
        $variant->position = 1;
        $variant->save();
        $data = $data_cate = array();
        if($variant->id > 0){
            foreach ($request->title as $key => $value) {
                $varTrans =FormAttributeTranslation::where(['attribute_id'=> $variant->id,'language_id'=>$request->language_id[$key] ])->first() ?? new FormAttributeTranslation();
                $varTrans->title = $request->title[$key];
                $varTrans->attribute_id = $variant->id;
                $varTrans->language_id = $request->language_id[$key];
                $varTrans->save();
            }
            $option_ids = $request->option_ids ? $request->option_ids : [];
            FormAttributeOption::where('attribute_id',$variant->id)->whereNotIn('id',$option_ids)->delete();

            foreach ($request->hexacode as $key => $value) {
                $opt_id = ($request->opt_id && isset($request->opt_id[0]) && isset($request->opt_id[0][$key] ) ) ? @$request->opt_id[0][$key] : '';


                $varOpt = FormAttributeOption::where(['attribute_id'=> $variant->id,'id'=>$opt_id  ])->first() ??  new FormAttributeOption();
                $varOpt->title = @$request->opt_color[0][$key];
                $varOpt->attribute_id = $variant->id;
                $varOpt->hexacode = (@$value == '') ? '' : @$value;
                $varOpt->save();

                foreach($request->language_id as $k => $v) {
                    $data[] = [
                        'title' => $request->opt_color[$k][$key],
                        'attribute_option_id' => $varOpt->id,
                        'language_id' => $v
                    ];
                }
            }
            FormAttributeOptionTranslation::insert($data);
        }
        $msg = __( 'Attribute added successfully!');
        if($request->attribute_for ==2){
            $msg = __( 'Driver Reviwes Question added successfully!');
        }
        return redirect()->back()->with('success',$msg);

    }

    public function delete($domain = '', $id){
        try{
            FormAttribute::where('id', $id)->update(['status'=>2]);
            return redirect()->back()->with('success', 'Attribute updated successfully!');

        } catch (Exception $e) {

            return response()->json([
                'status'=>'error',
                'message' => $e->getCode(),
                'data' => []
            ]);
        }

    }

    public function edit($domain = '',$id)
    {
        try{

            $attribute = FormAttribute::find($id);
            if (!$attribute) {
                return response()->json([
                'success' => false,
                'message' => 'Attribute not found.'
                ], 404);
            }
        $submitUrl = route('attribute.update', $id);

        $returnHTML = view('attributes.edit-attribute')->with(['attribute'=>$attribute])->render();
        return response()->json(array('success' => true, 'html'=>$returnHTML, 'submitUrl' => $submitUrl));

        }catch(\exception $e){
            return response()->json(["Error"=>$e->getMessage()]);
        }
}


    public function update(Request $request,$domain='', $id){
    try {
        $attribute = FormAttribute::find($id);
        if ($attribute) {
            $attribute->title = $request->title[0];

            if ($request->option_ids) {
                $existingOptionIDs = $request->option_ids;
                FormAttributeOption::where('attribute_id', $attribute->id)
                    ->whereNotIn('id', $existingOptionIDs)
                    ->delete();

                foreach ($request->option_ids as $key => $optionID) {
                    $optTitle = $request->opt_color[0][$key];
                    $optHexacode = $request->hexacode[$key];

                    $varOpt = FormAttributeOption::find($optionID) ?? new FormAttributeOption();
                    $varOpt->title = $optTitle;
                    $varOpt->hexacode = $optHexacode;
                    $varOpt->attribute_id = $attribute->id;
                    $varOpt->save();


                }
            }

            $attribute->save();
            $msg = __( 'Attribute updated successfully!');
            return redirect()->back()->with('success',$msg);
        }
        else {
            return response()->json([
                'success' => false,
                'message' => 'Attribute not found'
            ], 404);
        }
        } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error updating attribute: ' . $e->getMessage()
        ], 500);
    }
}


}
