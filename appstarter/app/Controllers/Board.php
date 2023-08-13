<?php

namespace App\Controllers;
use App\Models\BoardModel;//사용할 모델을 반드시 써줘야한다.
use CodeIgniter\I18n\Time; //Time 클래스를 사용할 수 있도록 

class Board extends BaseController
{
    public function list()
    {
        //$db = db_connect();
        //$query = "select * from board order by bid desc";
        //$rs = $db->query($query);
        //$data['list'] = $rs->getResult();//결과값 저장
    
        $boardModel = new BoardModel();
        $data['list'] = $boardModel->orderBy('bid', 'DESC')->findAll();
        return render('board_list', $data);//view에 리턴        
    }

    public function write()
    {        
        if(!isset($_SESSION['userid'])){
            //기존 방식
            //echo "<script>alert('로그인하십시오.');location.href='/login'</script>";
            //exit;

            //CI4 방식
            return redirect()->to('/login')->with('alert', '로그인하십시오.');
    }
        return render('board_write');  
    }
    

    public function view($bid = null)
    {
        //$db = db_connect();
        //$query = "select * from board where bid=".$bid;
        //$rs = $db->query($query);
        //$data['view'] = $rs->getRow();

        $boardModel = new BoardModel();
        $data['view'] = $boardModel->where('bid', $bid)->first();
        return render('board_view', $data);  //view에 리턴      
    }
    public function save()
    {
        if(!isset($_SESSION['userid'])){
            return redirect()->to('/login')->with('alert', '로그인하십시오.');
        }        
        $db = db_connect();
        /*
        $subject=$this->request->getVar('subject');
        $content=$this->request->getVar('content');
        $myTime = new Time('now', 'Asia/Seoul');//시간대를 Asia로 변경
        $myTime->modify('+9 hours');//기본시간에서 9시간 추가
        $formattedTime = $myTime->toDateTimeString(); //시간을 문자열로 변경후 변수에 할당

        $sql = "INSERT INTO board (userid, subject, content, regdate) VALUES ('test', ?, ?, ?)";
        $db->query($sql, [$subject, $content, $formattedTime]);
        */
        $data = [
            //'userid' => 'test',
            'userid' => $_SESSION['userid'],
            'subject' => $this->request->getVar('subject'),
            'content' => $this->request->getVar('content')
        ];
        
        $myTime = new Time('now', 'Asia/Seoul');
        $myTime->modify('+9 hours');
        $data['regdate'] = $myTime->toDateTimeString();
        
        $db->table('board')->insert($data);

        return $this->response->redirect(site_url('/board'));
    }    
}