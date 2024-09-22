<?php
class Pages extends Controller {
    public function __construct()
    {
        
    }

    public function index(){

        $data = [
            'title' => 'Ranjbar'
        ];

        $this->view('pages/index',$data);
    }

}