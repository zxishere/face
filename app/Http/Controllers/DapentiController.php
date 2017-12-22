<?php

namespace App\Http\Controllers;
use \Curl\Curl;
use Sunra\PhpSimple\HtmlDomParser;
use Illuminate\Http\Request;

class DapentiController extends Controller
{


    private function get($url){
        $curl = new Curl();
        $curl->setUserAgent('Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.89 Safari/537.36');
        $curl->setReferrer('http://www.dapenti.com');
        $curl->setHeader('CLIENT-IP', '127.0.0.1');
        $curl->setHeader('X-FORWARDED-FOR', '127.0.0.1');
        $curl->get($url);
        $curl->close();
        if ($curl->error) {
            response()->json(['status' => 'error', 'code' => $curl->errorCode, 'message' => $curl->errorMessage])->send();
            die();
        } else {
            return $curl->response;
        }
    }

    private function replaceImage($url){
        return str_replace('http://pic.yupoo.com/dapenti/', route('dapenti.image').'?url=', $url);
    }

    public function image(Request $request){
        $url = 'http://pic.yupoo.com/dapenti/'.$request->get('url');
        $curl = new Curl();
        $curl->setUserAgent('Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.89 Safari/537.36');
        $curl->setReferrer('http://www.dapenti.com');
        $curl->setHeader('CLIENT-IP', '127.0.0.1');
        $curl->setHeader('X-FORWARDED-FOR', '127.0.0.1');
        $curl->get($url);
        return response($curl->response, 200)->header('Content-Type', $curl->responseHeaders['Content-Type']);
        // return response($curl->response)->withHeaders($curl->responseHeaders);
    }


    public function index()
    {
        $url = 'http://www.dapenti.com/blog/index.asp';
        $response = $this->get($url);
        $html = HtmlDomParser::str_get_html( iconv('GBK',"UTF-8//IGNORE", $response) );
        $content = [];
        foreach($html->find('div.center_title_down',0)->find('li') as $i){
            $news['title'] = $i->plaintext;
            $news['link'] = route('dapenti.show', ['id' => explode('id=', $i->find('a',0)->href)[1]]);
            $content[] = $news;
        }
        return view('dapenti.site_index', ['title' => 'index', 'content' => $content]);
    }

    /*
    https://appb.dapenti.com/index.php?s=/home/api/tugua/p/1/limit/30
    https://appb.dapenti.com/index.php?s=/Home/api/lehuo/p/1/limit/10
    https://appb.dapenti.com/index.php?s=/Home/api/duanzi/p/1/limit/10
    */
/*    public function list($type)
    {
        $url = 'https://appb.dapenti.com/index.php?s=/home/api/'.$type.'/p/1/limit/30';
        $response = $this->get($url);
        $result = collect($response->data)->transform(function ($item, $key) {
            if ($item->title != 'AD') {
                $item->link = route('dapenti.show', ['id' => explode('id=', $item->link)[1]]);
                $item->imgurl = $this->replaceImage($item->imgurl);
                return $item;
            }
        });
        // return response()->json($result->filter()->values()->all());
        $content = $result->filter()->values()->all();
        // dd($content);
        return view('dapenti.list', ['title' => $type, 'content' => $content]);

    }*/

    public function show($id)
    {
        $url = 'http://www.dapenti.com/blog/more.asp?name=xilei&id='.$id;
        $response = $this->get($url);
        $plainthtml = HtmlDomParser::str_get_html( iconv('GBK',"UTF-8//IGNORE", $response));
        $title = explode('--', $plainthtml->find('title',0)->innertext())[1];
        $html = $plainthtml->find('div.oblog_text',0);
        foreach ($html->find('script') as $ad)
        {
            $ad->outertext = '';
        }
        foreach ($html->find('ins') as $ad)
        {
            $ad->outertext = '';
        }
        $strs = [
            '以下内容，有可能引起内心冲突或愤怒等不适症状',
            '本文转摘的各类事件，均来自于公开发表的国内媒体报道',
            '欢迎转载，转载请保证原文的完整性',
            '每天一图卦，让我们',
            '海外访问，请加',
            '友情提示：请各位河蟹评论',
            '喷嚏新浪围脖'
        ];
        foreach ($html->find('p') as $p)
        {
            foreach ($strs as $str) {
                if(str_contains($p->innertext, $str)){
                    $p->outertext = '';
                }
            }
            if (trim($p->plaintext == '')) {
                $p->outertext = '';
            }
        }
        $content = str_replace('<hr>广告<br><!-- bigbanner --><br><hr>', '',$html);
        $content = str_replace('<b>免责申明：</b>', '',$content);
        $content = $this->replaceImage($content);
        return view('dapenti.show', ['title' => $title, 'content' => $content]);
    }

}
