// +----------------------------------------------------------------------
// | Copyright (C) ����Ƽ� www.ihotte.com admin@ihotte.com
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author   ���ֱߵĻ��� QQ:858908467 E-mail:858908467@qq.com
// +----------------------------------------------------------------------
//**/
//+------------------------------------------------------------------------------
//* �ļ�$ID �� webftp.show.js
//+------------------------------------------------------------------------------
//* ·��$ID �� static/js/webftp.show.js
//+------------------------------------------------------------------------------
//* ����汾�� ���� WebFTP V1.0.0 2011-10-01
//+------------------------------------------------------------------------------
//* ���ܼ�飺 ͼƬԤ����ʼ���ӿ�
//+------------------------------------------------------------------------------
//* ע����� ����˽��ɾ���˰�Ȩ��Ϣ��
//+------------------------------------------------------------------------------
function init_show(){
	$("#list a[rel^='show']").live("click", function(event){		
		$("#list a[rel^='show']").colorbox({
			slideshow:true,transition:"elastic", width:"100%", height:"100%",bgOpacity:50,preloading:true
		});
		//��ֹ��һ�ε��Ĭ���¼�, colorbox��ĳЩ�������е�һ�ε����Ч��BUG
		if('show' == $(this).attr('rel')){
			event.preventDefault();
		}
		
	});
}