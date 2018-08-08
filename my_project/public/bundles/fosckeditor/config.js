/**
 *@licenseCopyright(c)2003-2018,CKSource-FredericoKnabben.Allrightsreserved.
 *Forlicensing,seehttps://ckeditor.com/legal/ckeditor-oss-license
 */

CKEDITOR.editorConfig=function(config){
//Definechangestodefaultconfigurationhere.Forexample:
//config.language='fr';
//config.uiColor='#AADC6E';
//config.toolbar=['bold','italic','|','undo','redo'];
    config.height="500px";
    config.width="816px";
    config.toolbar_Custom=[
        {name:'custom',items:['Save','Undo','Redo','Replace','Maximize','Templates',
                '-',
                'Font','FontSize','TextColor','BGColor','Format',
                '-',
                'JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock',
                '-',
                'Bold','Italic','Underline','Strike','Subscript','Superscript','RemoveFormat',
                '-',
                'Image', 'Youtube','Link','Unlink','Table','HorizontalRule','Source'
            ]}];

    config.extraPlugins = 'image,copyformatting,filebrowser,wordcount,notification,imagebrowser,youtube';
    config.filebrowserImageBrowseUrl = '/uploads/images';
    config.imageBrowser_listUrl = "/post/browseimages";
    config.youtube_privacy = false;
    config.toolbar='Custom';
    config.contentsCss = '/fonts.css';
    config.font_names =
        'HeadlandOne-Regular/HeadlandOne-Regular'+'Arial/Arial, Helvetica, sans-serif;' +
        'Times New Roman/Times New Roman, Times, serif;' +
        'Verdana';

    config.font_names = 'HeadlandOne-Regular; Arial;Times New Roman;Verdana';



};