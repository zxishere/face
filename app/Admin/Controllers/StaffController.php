<?php

namespace App\Admin\Controllers;

use App\Models\Staff;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class StaffController extends Controller
{
    use ModelForm;

     const SEX = [0 => '女', 1 => '男'];
    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('Staffs');
            $content->description('list');

            $content->body($this->grid());
        });
    }

    /**
     * Edit interface.
     *
     * @param $id
     * @return Content
     */
    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id) {

            $content->header('header');
            $content->description('description');

            $content->body($this->form()->edit($id));
        });
    }

    /**
     * Create interface.
     *
     * @return Content
     */
    public function create()
    {
        return Admin::content(function (Content $content) {

            $content->header('header');
            $content->description('description');

            $content->body($this->form());
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {

        return Admin::grid(Staff::class, function (Grid $grid) {
            // 第一列显示id字段，并将这一列设置为可排序列
            // $grid->id('ID')->sortable();
            // 不存在的`latest_face`字段
            $grid->column('latest_face')->display(function () {
                return asset('storage/staffs/'.$this->id.'.jpg') ;
            })->image('50','50');

            $grid->name('Name')->sortable();
            // $grid->title()->editable();
            $grid->gender()->editable('select', self::SEX)->sortable();
            $grid->column('latest')->sortable();

            $grid->filter(function($filter){
                // 去掉默认的id过滤器
                $filter->disableIdFilter();
                // 在这里添加字段过滤器
                $filter->like('name', 'name');
                $filter->equal('gender')->select(self::SEX);

            });

            $grid->disableCreation();
            $grid->disableRowSelector();
            $grid->disableExport();
            $grid->disableActions();

        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Staff::class, function (Form $form) {

            $form->display('name');
            $form->text('title');
            $form->select('gender')->options(self::SEX);
            // $form->display('created_at', 'Created At');
            // $form->display('updated_at', 'Updated At');
        });
    }
}
