<?php
namespace App\Models;  
use CodeIgniter\Model;

class FileModel extends Model{
    protected $table = 'file_table';//사용하는 테이블
    protected $returnType     = 'object';//이값이 없으면 기본이 array가 된다.
    //사용할 컬럼지정
    protected $primaryKey = 'fid';
    protected $allowedFields = [        
        'bid',
        'userid',
        'filename',
        'regdate',
        'status',
        'memoid',
        'type'
    ];
}