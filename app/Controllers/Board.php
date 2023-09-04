<?php

namespace App\Controllers;
use App\Models\BoardModel;//사용할 모델을 반드시 써줘야한다.
use App\Models\FileModel;//사용할 모델을 반드시 써줘야한다.
use CodeIgniter\I18n\Time; //Time 클래스를 사용할 수 있도록 
use CodeIgniter\Pager\Pager; //페이저를 사용할 수 있도록 로드

class Board extends BaseController
{
    public function list()
    {
        $model = new BoardModel(); // BoardModel 인스턴스 생성
        $page = $this->request->getVar('page') ?? 1;
        $perPage = 10;
        $startLimit = ($page - 1) * $perPage;

        // 데이터베이스에서 게시물 가져오기
        $query = $model->select('*') // 모든 컬럼을 선택합니다.
            ->where('1=1')
            ->orderBy('bid', 'desc')
            //->limit($perPage, $startLimit) // LIMIT 구문 추가
            ->findAll($perPage, $startLimit); // 수정된 부분

        // 전체 게시물 수 구하기
        $total = $model->countAllResults();

        // Pager service 가져오기
        $pager = service('pager');

        // 페이저 생성
        $pager_links = $pager->makeLinks($page, $perPage, $total, 'default_full');

        // 뷰에 전달할 데이터 구성
        $data['list'] = $query;
        $data['total'] = $total;
        $data['page'] = $page;
        $data['perPage'] = $perPage;
        $data['pager_links'] = $pager_links;

        return render('board_list', $data); // view() 함수로 뷰를 로드
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

        $data['view'] = $boardModel->select('board.*, GROUP_CONCAT(file_table.filename) as fs')
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

        //$file = $this->request->getFile('upfile');//첨부한 파일의 정보를 가져온다.
        
        $db = db_connect();
         //새글 등록시 생성될 id 할당, 쿼리빌더에서 사용불가라 db객체 활용

        //$file = $this->request->getFile('upfile');//첨부한 파일의 정보를 가져온다.
        $files = $this->request->getFileMultiple("upfile"); //다중 업로드 파일 정보
        $filepath = array();
        foreach($files as $file){
            if($file->getName()){//파일 정보가 있으면 저장한다.
                $filename = $file->getName();//기존 파일명을 저장할때 필요하다. 여기서는 사용하지 않는다.
                //$filepath = WRITEPATH. 'uploads/' . $file->store(); 매뉴얼에 나와있는 파일 저장 방법이다.여기서는 안쓴다.
                $newName = $file->getRandomName();//서버에 저장할때 파일명을 바꿔준다.
                $filepath[] = $file->store('board/', $newName);//CI4의 store 함수를 이용해 저장한다. 저장한 파일의 경로와 파일명을 리턴, 배열로 저장한다.
            }
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
            foreach($filepath as $fp){//배열로 저장한 파일 저장 정보를 디비에 입력한다.
                $fileData = [
                    //'userid' => 'test',
                    'bid' => $insertid,
                    'userid' => $_SESSION['userid'],
                    'filename' => $fp,
                    'type' => 'board'
                ];         
                $fileModel->insert($fileData);
            }

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
        $board = $boardModel->find($bid);//bid와 일치하는 글 조회, 객체에 할당

        if ($board && session('userid') == $board->userid) { //글이 있고 로그인한 유저가 글을 쓴 유저라면

            $fileModel = new FileModel();
            $files = $fileModel->where('type', 'board')->where('bid', $bid)->findAll();
    
            foreach ($files as $file) {
                unlink('uploads/' . $file->filename); //bid와 일치하는 파일 모두 삭제
            }
    
            // Delete file records, 테이블에서 데이터 삭제
            $fileModel->where('type', 'board')->where('bid', $bid)->delete();
            
            $boardModel->delete($bid);
            return redirect()->to(site_url('/board'));
        } else {
            return redirect()->to('/login')->with('alert', '본인이 작성한 글만 삭제할 수 있습니다.');
        } 
    }
    
}