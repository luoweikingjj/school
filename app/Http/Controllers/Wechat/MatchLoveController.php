<?php
/**
 * Created by PhpStorm.
 * User: xuxiaodao
 * Date: 2017/11/28
 * Time: 下午3:46
 */

namespace App\Http\Wechat;


use App\Http\Controllers\Controller;
use App\Http\Logic\MatchLoveLogic;
use App\Http\Logic\PaginateLogic;
use App\MatchLove;
use App\User;

class MatchLoveController extends Controller
{
    /**
     * 新增
     *
     * @author yezi
     *
     * @return mixed
     */
    public function save()
    {
        $user = request()->input('user');
        $username = request()->input('username');
        $matchName = request()->input('match_name');
        $content = request()->input('content');
        $private = request()->input('private');


        $matchLove = new MatchLoveLogic();
        $result = $matchLove->createMatchLove($user->id,$username,$matchName,$content,$private,$user->{User::FIELD_ID_COLLEGE});

        return $result;
    }

    /**
     * 获取
     *
     * @author yezi
     *
     * @return mixed
     */
    public function matchLoves()
    {
        $user = request()->input('user');
        $pageSize = request()->input('page_size',10);
        $pageNumber = request()->input('page_number',1);

        $pageParams = ['page_size'=>$pageSize, 'page_number'=>$pageNumber];

        $query = MatchLove::query()->orderBy(MatchLove::FIELD_CREATED_AT,'desc');
        if($user->{User::FIELD_ID_COLLEGE}){
            $query->where(MatchLove::FIELD_ID_COLLEGE,$user->{User::FIELD_ID_COLLEGE});
        }

        $saleFriends = app(PaginateLogic::class)->paginate($query,$pageParams, '*',function($saleFriend)use($user){
            return app(MatchLoveLogic::class)->formatSingle($saleFriend,$user);
        });

        return $saleFriends;
    }

    /**
     * 详情
     *
     * @author yezi
     *
     * @param $id
     * @return mixed
     */
    public function detail($id)
    {
        $user = request()->input('user');

        $matchLove = MatchLove::find($id);

        $matchLoveLogic = new MatchLoveLogic();

        return $matchLoveLogic->formatSingle($matchLove);
    }

    /**
     * 删除
     *
     * @author yezi
     *
     * @param $id
     * @return mixed
     */
    public function delete($id)
    {
        $user = request()->input('user');

        $result = MatchLove::where(MatchLove::FIELD_ID,$id)->where(MatchLove::FIELD_ID_OWNER,$user->id)->delete();

        return $result;
    }

}