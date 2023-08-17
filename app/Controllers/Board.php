<?php

namespace App\Controllers;
use App\Models\BoardModel;//사용할 모델을 반드시 써줘야한다.
use App\Models\FileModel;//사용할 모델을 반드시 써줘야한다.
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
        //$data['view'] = $rs->getRow(); 쿼리 결과를 $data배열내 키값 view에 할당

        $boardModel = new BoardModel();
        $fileModel = new FileModel();

        $data['view'] = $boardModel->select('board.*, file_table.filename')
                            ->join('file_table', 'file_table.bid = board.bid', 'left')
                            ->where('file_table.type', 'board')
                            ->where('board.bid', $bid)
                            ->first();

        return render('board_view', $data);  //view에 리턴 
    }
    public function save()
    {
        if(!isset($_SESSION['userid'])){
            return redirect()->to('/login')->with('alert', '로그인하십시오.');
        }        
        $boardModel = new BoardModel();
        $fileModel = new FileModel();
        $data = [
            //'userid' => 'test',
            'userid' => $_SESSION['userid'],
            'subject' => $this->request->getVar('subject'),
            'content' => $this->request->getVar('content')
        ];
        
        $myTime = new Time('now', 'Asia/Seoul');
        $myTime->modify('+9 hours');
        $data['regdate'] = $myTime->toDateTimeString();
        
        $bid = $this->request->getVar('bid');//bid값이 있으면 수정이고 아니면 등록이다.

        $file = $this->request->getFile('upfile');//첨부한 파일의 정보를 가져온다.
        
        $db = db_connect();
         //새글 등록시 생성될 id 할당, 쿼리빌더에서 사용불가라 db객체 활용

        if($file->getName()){//파일 정보가 있으면 저장한다.
            $filename = $file->getName();//기존 파일명을 저장할때 필요하다. 여기서는 사용하지 않는다.
            //$filepath = WRITEPATH. 'uploads/' . $file->store(); 매뉴얼에 나와있는 파일 저장 방법이다.여기서는 안쓴다.
            $newName = $file->getRandomName();//서버에 저장할때 파일명을 바꿔준다.
            $filepath = $file->store('board/', $newName);//CI4의 store 함수를 이용해 board폴더에 저장한다.
        }   

        if($bid){ //값이 있으면 수정
            if ($board && session('userid') == $board->userid) { //글작성한 유저라면
                $boardModel->update($bid, $data);
                return redirect()->to(site_url('/boardView/' . $bid));
            }else{ //글작성한 유저가 아니라면
                return redirect()->to('/login')->with('alert', '본인이 작성한 글만 수정할 수 있습니다.');
            }
        } else{ //없으면 신규 등록  
            $boardModel->insert($data);//글 등록
            $insertid = $db->insertID(); //글 등록후 생기는 id(bid)를 $insertid에 할당
            $fileData = [
                //'userid' => 'test',
                'bid' => $insertid,
                'userid' => $_SESSION['userid'],
                'filename' => $filepath,
                'type' => 'board'
            ];     

            $fileModel->insert($fileData);
            return $this->response->redirect(site_url('/board'));
        }
    }

    public function modify($bid = null)
    {

        /* 쿼리 직접 작성
        $db = db_connect();
        $query = "select * from board where bid=".$bid;
        $rs = $db->query($query);
        if($_SESSION['userid']==$rs->getRow()->userid){
            $data['view'] = $rs->getRow();
            return render('board_write', $data);  
        }else{
            echo "<script>alert('본인이 작성한 글만 수정할 수 있습니다.');location.href='/login';</script>";
            exit;
        }
        */

        //모델 활용 작성
        $boardModel = new BoardModel();
        $board = $boardModel->find($bid);

        if ($board && session('userid') == $board->userid) {
            $data['view'] = $board;
            return render('board_write', $data);  
        } else {
            return redirect()->to('/login')->with('alert', '본인이 작성한 글만 수정할 수 있습니다.');
        }
    }

    public function delete($bid = null)
    {
        $boardModel = new BoardModel();
        $board = $boardModel->find($bid);

        if ($board && session('userid') == $board->userid) {
            $boardModel->delete($bid);
            return redirect()->to(site_url('/board'));
        } else {
            return redirect()->to('/login')->with('alert', '본인이 작성한 글만 삭제할 수 있습니다.');
        } 
    }
    
}