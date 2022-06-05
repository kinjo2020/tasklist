<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Task;

class Taskscontroller extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = [];
        
        if (\Auth::check()){
            //認証済みユーザを取得
            $user = \Auth::user();
            //ユーザのタスクを取得
            $tasks = $user->tasks()->paginate(10);
            
            $data = [
                'user' => $user,
                'tasks' => $tasks
            ];
            
            return view('tasks.index', $data);
        }
        
        else {
            return view('tasks.index');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $task = new Task;
        
        return view('tasks.create', [
            'task' => $task,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //バリデーション
        $request->validate([
            'content' => 'required|max:255',
            'status' => 'required|max:10',
        ]);
        
        //認証済みユーザのタスクを作成
        $request->user()->tasks()->create([
            'content' => $request->content,
            'status' => $request->status
        ]);
        
        return redirect('/');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //idの値でタスクを取得
        $task = Task::findOrfail($id);
        
        //認証済みユーザ自身のタスク詳細を表示
        if (\Auth::id() === $task->user_id){
            
            return view('tasks.show', [
                'task' => $task,
            ]);
        }
        
        //それ以外はトップページはリダイレクト
        else {
            return redirect('/');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $task = Task::findOrfail($id);
        
        if (\Auth::id() === $task->user_id){
            return view('tasks.edit', [
            'task' => $task,
            ]);
        }
        
        else {
            return redirect('/');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //バリデーション
        $request->validate([
            'content' => 'required|max:255',
            'status' => 'required|max:10',
        ]);
        
        //指定のタスクidを取得
        $task = Task::findOrfail($id);
        
        //取得したタスクを更新
        $task->update([
            $task->content = $request->content,
            $task->status = $request->status,
            // $task->user_id = $request->user_id,
        ]);
        
        return redirect('/');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $task = Task::findOrfail($id);
        
        $task->delete();
        
        return redirect('/');
    }
}
