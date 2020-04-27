var activitySwper = new Vue({
    el:'#activity-swiper',
    data:{
        flkty:'',
        picked:0,
    },
    mounted:function(){
        if(!this.$refs.swiper) return;
        var that = this;
        this.flkty = new Flickity( this.$refs.swiper, {
            contain: true,
            autoPlay: false,
            pageDots: false,
            prevNextButtons: false,
            wrapAround: true,
            cellAlign: 'left',
            on: {
                change: function( index ) {
                    that.picked = index;
                }
            }
        });
    },
    methods:{
        select:function(index){
            this.flkty.select(index );
            this.picked = this.flkty.selectedIndex;
        }
    }
})

var activityPublic = new Vue({
    el:'#activity-public',
    data:{
        //编辑器
        editor:'',
        toolbarOptions:[
            ['bold', 'italic', { 'header': 2 }],
            [{ 'color': [] }, { 'background': [] }],
            ['blockquote', 'code-block'],
            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
            ['link','imagei','videoi'],
            [{ 'align': [] },'divider'],
            ['clean'],
            ['undo','redo'],
        ],
        showMediaForm:false,
        mediaType:'',
        insertVideoUrl:true,
        insertVideoFile:false,
        insertVideoButton:false,
        videoError:'',
        dropenter:false,
        progress:'0%',
        thumbUPlocked:false,
        videoText:'',
        videoSize:'10',
        postIdOrUrl:'',
        getPostLocked:false,
        getPostError:'',
        imageBoxType:'upload',
        insertUri:'',
        nonce:'',
        //活动属性
        time:{
            'registration_time':{
                'year':'00',
                'month':'00',
                'day':'00',
                'hh':'00',
                'mn':'00'
            },
            'end_registration_time':{
                'year':'00',
                'month':'00',
                'day':'00',
                'hh':'00',
                'mn':'00'
            },
            'start_time':{
                'year':'00',
                'month':'00',
                'day':'00',
                'hh':'00',
                'mn':'00'
            },
            'end_time':{
                'year':'00',
                'month':'00',
                'day':'00',
                'hh':'00',
                'mn':'00'
            }
        },
        address:'',
        key:'free',
        count:'',
        rmb:'',
        lv:[],
        thumb:'',
        thumbSrc:'',
        title:'',
        content:'',
        publicLocked:false,
        error:'',
        buttonText:'',
        success:false,
        type:'',
        filesArg:[]
        
    },
    mounted:function(){
        if(!this.$refs.editor) return;
        this.nonce = this.$refs.nonce.value;

        //初始项目
        this.videoSize = activity_script.videoSize;
        this.thumbSrc = activity_script.thumbSrc;
        this.thumb = activity_script.thumb;
        this.title = activity_script.title;
        this.filesArg = activity_script.filesArg;

        this.count = this.$refs.peopleCount.getAttribute('data-count');
        this.address = this.$refs.address.getAttribute('data-address');
        this.key = this.$refs.key.getAttribute('data-key');
        this.rmb = this.$refs.rmb.getAttribute('data-rmb');
        var _lv = this.$refs.roleData.getAttribute('data-role');
        _lv = _lv.substring(0,_lv.length-1);
        this.lv = _lv.split(",");

        this.time = {
            'registration_time':{
                'year':this.$refs.reg.getAttribute('data-year'),
                'month':this.$refs.reg.getAttribute('data-month'),
                'day':this.$refs.reg.getAttribute('data-day'),
                'hh':this.$refs.reg.getAttribute('data-hh'),
                'mn':this.$refs.reg.getAttribute('data-mn')
            },
            'end_registration_time':{
                'year':this.$refs.endreg.getAttribute('data-year'),
                'month':this.$refs.endreg.getAttribute('data-month'),
                'day':this.$refs.endreg.getAttribute('data-day'),
                'hh':this.$refs.endreg.getAttribute('data-hh'),
                'mn':this.$refs.endreg.getAttribute('data-mn')
            },
            'start_time':{
                'year':this.$refs.start.getAttribute('data-year'),
                'month':this.$refs.start.getAttribute('data-month'),
                'day':this.$refs.start.getAttribute('data-day'),
                'hh':this.$refs.start.getAttribute('data-hh'),
                'mn':this.$refs.start.getAttribute('data-mn')
            },
            'end_time':{
                'year':this.$refs.end.getAttribute('data-year'),
                'month':this.$refs.end.getAttribute('data-month'),
                'day':this.$refs.end.getAttribute('data-day'),
                'hh':this.$refs.end.getAttribute('data-hh'),
                'mn':this.$refs.end.getAttribute('data-mn')
            }
        };
        
        //自定义编辑器图标
        var icon = Quill.import('ui/icons'),
        that = this;
        icon['divider'] = '<i class="iconfont zrz-icon-font-divider"></i>';
        icon['imagei'] = icon['image'];
        icon['videoi'] = icon['video'];
        icon['redo'] = '<i class="iconfont zrz-icon-font-zhongzuo"></i>';
        icon['undo'] = '<i class="iconfont zrz-icon-font-chexiao"></i>';

        'use strict';

        var BlockEmbed = Quill.import('blots/block/embed');

        //分割线
        var DividerBlot = function (_BlockEmbed) {
            _inherits(DividerBlot, _BlockEmbed);

            function DividerBlot() {
            _classCallCheck(this, DividerBlot);

            return _possibleConstructorReturn(this, (DividerBlot.__proto__ || Object.getPrototypeOf(DividerBlot)).apply(this, arguments));
            }

            return DividerBlot;
        }(BlockEmbed);

        DividerBlot.blotName = 'divider';
        DividerBlot.tagName = 'hr';
        Quill.register(DividerBlot);

        var BlockEmbed = Quill.import('blots/block/embed');

        //图片
        var ImageBlot = function (_BlockEmbed) {
            _inherits(ImageBlot, _BlockEmbed);

            function ImageBlot() {
                _classCallCheck(this, ImageBlot);

                return _possibleConstructorReturn(this, (ImageBlot.__proto__ || Object.getPrototypeOf(ImageBlot)).apply(this, arguments));
            }

            _createClass(ImageBlot, null, [{
                key: 'create',
                value: function create(value) {
                    var node = _get(ImageBlot.__proto__ || Object.getPrototypeOf(ImageBlot), 'create', this).call(this);
                    node.setAttribute('src', value.url);
                    node.setAttribute('class', value.class);
                    node.id = value.id;
                    return node;
                }
            }, {
                key: 'value',
                value: function value(node) {
                    return {
                        alt: node.getAttribute('alt'),
                        url: node.getAttribute('src'),
                        id: node.id,
                        class:node.getAttribute('class')
                    };
                }
            }]);

            return ImageBlot;
        }(BlockEmbed);

        ImageBlot.blotName = 'imagei';
        ImageBlot.tagName = 'img';
        ImageBlot.className = 'bbp-img';
        Quill.register(ImageBlot);

        //视频
        var VideoBlot = function (_BlockEmbed3) {
            _inherits(VideoBlot, _BlockEmbed3);

            function VideoBlot() {
            _classCallCheck(this, VideoBlot);

            return _possibleConstructorReturn(this, (VideoBlot.__proto__ || Object.getPrototypeOf(VideoBlot)).apply(this, arguments));
            }

            _createClass(VideoBlot, null, [{
            key: 'create',
            value: function create(value) {
                var node = _get(VideoBlot.__proto__ || Object.getPrototypeOf(VideoBlot), 'create', this).call(this);
                node.className += ' content-video content-box img-bg';
                node.setAttribute('contenteditable', 'false');
                if (value.url) {
                node.setAttribute('data-video-url', value.url);
                node.setAttribute('id', value.id);
                }
                if(value.thumb){
                    node.setAttribute('data-video-thumb', value.thumb);
                }
                if(value.title){
                    node.setAttribute('data-video-title', value.title);
                }
                var i = document.createElement('span');
                i.setAttribute('class', 'remove-img-ico click');
                i.innerText = '删除';
                node.appendChild(i);
                return node;
            }
            }, {
            key: 'value',
            value: function value(node) {
                return {
                    title:node.getAttribute('data-video-title'),
                thumb:node.getAttribute('data-video-thumb'),
                url: node.getAttribute('data-video-url'),
                id: node.getAttribute('id')
                };
            }
            }]);

            return VideoBlot;
        }(BlockEmbed);

        VideoBlot.blotName = 'videoUrl';
        VideoBlot.tagName = 'div';
        VideoBlot.className = 'content-video-box';
        Quill.register(VideoBlot);

        //本地视频
        var VideoFileBlot = function (_BlockEmbed4) {
            _inherits(VideoFileBlot, _BlockEmbed4);

            function VideoFileBlot() {
            _classCallCheck(this, VideoFileBlot);

            return _possibleConstructorReturn(this, (VideoFileBlot.__proto__ || Object.getPrototypeOf(VideoFileBlot)).apply(this, arguments));
            }

            _createClass(VideoFileBlot, null, [{
            key: 'create',
            value: function create(value) {
                var node = _get(VideoFileBlot.__proto__ || Object.getPrototypeOf(VideoFileBlot), 'create', this).call(this);
                node.className += ' content-video content-box';
                node.setAttribute('contenteditable', 'false');
                if (value.src) {
                var video = document.createElement('video');
                video.src = value.src;
                video.autoPlay = false;
                video.setAttribute('controls', 'controls');

                var i = document.createElement('span');
                i.setAttribute('class', 'remove-img-ico click');
                i.innerText = '删除';
                node.appendChild(video);
                node.appendChild(i);
                }
                return node;
            }
            }, {
            key: 'value',
            value: function value(node) {
                return {
                src: node.querySelectorAll('video')[0].getAttribute('src')
                };
            }
            }]);

            return VideoFileBlot;
        }(BlockEmbed);

        VideoFileBlot.blotName = 'videoFile';
        VideoFileBlot.tagName = 'div';
        VideoFileBlot.className = 'content-video-file-box';
        Quill.register(VideoFileBlot);

        var Clipboard = Quill.import('modules/clipboard'),
            Delta = Quill.import('delta');
        var PlainTextClipboard = function (_Clipboard) {
            _inherits(PlainTextClipboard, _Clipboard);

            function PlainTextClipboard() {
            _classCallCheck(this, PlainTextClipboard);

            return _possibleConstructorReturn(this, (PlainTextClipboard.__proto__ || Object.getPrototypeOf(PlainTextClipboard)).apply(this, arguments));
            }

            _createClass(PlainTextClipboard, [{
            key: 'onPaste',
            value: function onPaste(e) {
                if (e.defaultPrevented || !this.quill.isEnabled()) return;
                var range = this.quill.getSelection();
                var delta = new Delta().retain(range.index);

                if (e && e.clipboardData && e.clipboardData.types && e.clipboardData.getData) {
                var text = (e.originalEvent || e).clipboardData.getData('text/html');
                if(!text){
                    return true;
                }
                var cleanedText = this.convert(text);

                e.stopPropagation();
                e.preventDefault();

                delta = delta.concat(cleanedText).delete(range.length);
                this.quill.updateContents(delta, Quill.sources.USER);

                this.quill.setSelection(delta.length() - range.length, Quill.sources.SILENT);
                return false;
                }
            }
            }]);

            return PlainTextClipboard;
        }(Clipboard);

        Quill.register('modules/clipboard', PlainTextClipboard);

        var bindings = {
            exitBlockWithEnter: {
                key: 'enter',
                format: ['blockquote', 'list'],
                collapsed: true,
                empty: true,
                handler: function(range, context) {
                    that.editor.formatText(range.index, range.length + 1, {blockquote: null, list: null});
                    return false;
                }
            }
        }

        //初始化编辑器
        this.editor = new Quill(this.$refs.editor,{
            modules: {
                syntax: zrz_script.highlight == 1 ? true : false,
                toolbar:{
                    container: this.toolbarOptions,
                    handlers: {
                    'imagei': this.imageHandler,
                    'divider':this.dividerHandler,
                    'videoi':this.videoHandler,
                    'undo':this.undo,
                    'redo': this.redo
                }
                },
                keyboard: { bindings: bindings }
            },
            placeholder: '从这里开始...',
            //readOnly: false,
            theme: 'snow',
            scrollingContainer:document.documentElement,
        });

        //拖拽上传
        this.dropFile();

        //跟随滚动
        document.getElementById('toolbar').appendChild(document.querySelectorAll('.ql-toolbar')[0]);
        setTimeout(function () {
            var toolbarButton = document.querySelectorAll('.ql-icon-picker');
            for (var i = 0; i < toolbarButton.length; i++) {
                toolbarButton[i].addEventListener('click mousedown mousemove', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                });
            }
        }, 1000);
        this.content = this.editor.clipboard.dangerouslyPasteHTML(0, this.domRest(activity_script.content,'in'));
    },
    methods:{
        undo:function(){
            this.editor.history.undo();
        },
        redo:function(){
            this.editor.history.redo();
        },
        //拖拽上传
        dropFile:function(){
            var box = this.$refs.dropbox,
                that = this;
            box.addEventListener('dragenter',function(event){
                event.preventDefault();
                event.stopPropagation();
                that.dropenter = true;
                },false);
            box.addEventListener('dragleave',function(event){
                event.preventDefault();
                event.stopPropagation();
                that.dropenter = false;
            },false);
            box.addEventListener('dragover',function(event){
                event.preventDefault();
                event.stopPropagation();
                that.dropenter = true;
                event.dataTransfer.effectAllowed = "copy";
            },false);
             box.addEventListener('drop', function(event){
                 event.preventDefault();
                 event.stopPropagation();
                 that.updateVideo(event,'file');
             }, false);
        },
        //图片上传事件
        imageHandler:function(thumb){
            this.mediaType = 'image';
            this.showMediaForm = true;
        },
        uploadThumb:function(){
            this.$refs.getFileOne.click();
            this.thumbLocked = true;
        },
        imageUpload:function(){
            this.mediaType = '';
            this.showMediaForm = false;
            this.insertUri = '';
            this.imageBoxType = 'upload';
            this.$refs.getFile.click();
        },
        imgUpload:function(event){
            if(!event.target.files) return;
            if(this.imgUploadLocked == true) return;
            this.imgUploadLocked = true;
            var files = event.target.files,
                that = this,
                filesArr = Array.prototype.slice.call(files),
                i = 0,
                key;
                if(this.thumbLocked){
                    this.thumbUPlocked = true;
                }

            filesArr.forEach(function(f) {
                if(!f.type.match("image.*")) {
                    return;
                }

                //生成随机数
                var id = uuid(8, 16);

                imgcrop(f,zrz_script.media_setting.max_width,'',function(resout){
                    if(resout[0] === true){
                        imgload(resout[1],function(imgSize){

                            if(!that.thumbLocked){
                                //编辑器插入临时图像
                                var range = that.editor.getSelection(true);
                                that.editor.insertText(range.index, '\n', Quill.sources.USER);
                                that.editor.insertEmbed(range.index, 'imagei', {
                                    url: zrz_script.theme_url+'/images/load-img.gif',
                                    id:id,
                                    class:'img-loading'
                                }, Quill.sources.USER);
                                that.editor.setSelection(range.index + 1, Quill.sources.SILENT);
                            }

                            //上传
                            var formData = new FormData();
                            if(f.type.indexOf('gif') > -1){
                                fileData = f;
                            }else{
                                fileData = resout[2];
                            }

                            //区分小图和大图
                            formData.append("type", 'big');
                            
                            formData.append('file',fileData,f.name);
                            formData.append("security", that.nonce);
                            formData.append("user_id", zrz_script.current_user);

                            axios.post(zrz_script.ajax_url+'zrz_media_upload',formData).then(function(resout){
                                console.log(resout);
                                if(resout.data.status == 200){
                                    if(!that.thumbLocked){
                                        var dom = document.getElementById(id);
                                        //上传成功，替换网址
                                        if(resout.data.status === 200){
                                            dom.src = resout.data.Turl;
                                        }else{
                                            //上传失败删除临时dom
                                            dom.parentNode.remove();
                                        }
                                    }else{
                                        that.thumb = resout.data.imgdata;
                                        that.thumbSrc = resout.data.Turl;
                                        that.thumbLocked = false;
                                        that.thumbUPlocked = false;
                                    }
                                    that.$refs.getFile.value = '';
                                    that.$refs.getFileOne.value = '';
                                    that.imgUploadLocked = false;
                                    if(resout.data.imgdata){
                                        that.filesArg.push(resout.data.imgdata);
                                    }
                                }
                            })
                        })
                    }
                })
            })
        },
        insetImageUri:function(){
            if(!this.insertUri) return;
            var that = this,
                range = that.editor.getSelection(true);
            that.editor.insertText(range.index, '\n', Quill.sources.USER);
            that.editor.insertEmbed(range.index, 'imagei', {
                url: that.insertUri,
                class:'bbp-img'
            }, Quill.sources.USER);
            that.editor.setSelection(range.index + 1, Quill.sources.SILENT);

            Vue.nextTick(function(){
                that.mediaType = '';
                that.showMediaForm = false;
                that.insertUri = '';
                that.imageBoxType = 'upload';
            })
        },
        //视频上传
        videoHandler:function(){
            this.showMediaForm = true;
            this.mediaType = 'video';
            this.videoText = '选择视频文件<b class="gray"> 或 </b>拖拽到此处';
        },
        //插入视频
        closeViedoForm:function(){
            this.showMediaForm = false
        },
        updateVideo:function(event,type){
            if(this.thumbLocked == true) return;
            this.thumbLocked = true;

            this.videoError = '';
            var that = this;
            if(type == 'url'){
                if(this.insertVideoButton == true) return;
                this.insertVideoButton = true;
                var data = {
                    'url':this.$refs.videoUrl.value,
                    'type':'url',
                    'security':this.nonce
                };
                axios.post(zrz_script.ajax_url+'zrz_video_upload',Qs.stringify(data)).then(function(resout){
                    if(resout.data.status == 200){
                        if(resout.data.msg.indexOf('smartideo') != -1){
                            if(!that.thumbUPlocked){
                                var range = that.editor.getSelection(true);
                                var thumb = '',
                                    title = '';
                                if(resout.data.img){
                                    thumb = resout.data.img.url.url;
                                    title = resout.data.img.title;
                                }
                                that.editor.insertText(range.index, '\n', Quill.sources.USER);
                                that.editor.insertEmbed(range.index, 'videoUrl', {
                                    title:title,
                                    thumb:thumb,
                                    url: that.$refs.videoUrl.value,
                                    id:uuid(8, 16)
                                }, Quill.sources.USER);
                                that.editor.setSelection(range.index + 1, Quill.sources.SILENT);
                                setTimeout(function () {
                                    videoBackground();
                                }, 300);
                            }else{
                                that.thumbVideo = that.$refs.videoUrl.value;
                                that.thumbVideoDom = resout.data.msg;
                                that.thumbUPlocked = false;
                                that.thumbSrc = '';
                                that.thumb = 0;
                            }
                            that.showMediaForm = false;
                            that.$refs.videoUrl.value = '';
                            that.insertVideoButton = false;
                        }else{
                            that.videoError = '不支持此视频，请重试';
                            that.thumbUPlocked = false;
                        }
                    }else{
                        that.videoError = resout.data.msg+'请重试';
                        that.insertVideoButton = false;
                        that.thumbUPlocked = false;
                    }
                    that.thumbLocked = false;
                })
            }

            if(type == "file"){

                if(event.dataTransfer){
                    var file = event.dataTransfer.files[0];
                }else{
                    var file = event.target.files[0]
                }

                if(!file){
                    this.thumbLocked = false;
                    return;
                }

                //视频尺寸限制
                if(Math.round(file.size/1024*100)/100000 > this.videoSize){
                    this.videoError = '该视频体积太大，请重新选择。';
                    this.thumbLocked = false;
                    return;
                }

                if(file.type.indexOf('video') != -1){
                    this.dropenter = false;
                    var data = {
                        'file':file,
                        'type':'file'
                    };

                    formData = new FormData();
                    formData.append("type", 'file');
                    formData.append('file',file,file.name);
                    formData.append("security", that.nonce);
                    var config = {
                        onUploadProgress: function(progressEvent){
                            var complete = (progressEvent.loaded / progressEvent.total * 100 | 0) + '%';
                            that.progress = complete;
                        }
                    }

                    axios.post(zrz_script.ajax_url+'zrz_video_upload',formData,config)
                    .then(function(resout){
                        console.log(resout);
                        if(resout.data.status == 200){
                            if(!that.thumbUPlocked){
                                that.videoText = '<span class="green">上传成功，插入中...</span>';
                                var range = that.editor.getSelection(true);
                                that.editor.insertText(range.index, '\n', Quill.sources.USER);
                                that.editor.insertEmbed(range.index, 'videoFile', {
                                    src: resout.data.Turl
                                }, Quill.sources.USER);
                                that.editor.setSelection(range.index + 1, Quill.sources.SILENT);
                                if(resout.data.imgdata){
                                    that.filesArg.push(resout.data.imgdata);
                                }
                            }else{
                                if(resout.data.imgdata){
                                    that.filesArg.push(resout.data.imgdata);
                                }
                                that.thumbVideo = resout.data.Turl;
                                that.thumbVideoDom = '<video src="'+resout.data.Turl+'" controls="controls"></video>';
                                that.thumbSrc = '';
                                that.thumb = 0;
                                that.thumbUPlocked = false;
                            }

                            that.showMediaForm = false;
                        }else{
                            that.videoError = resout.data.msg || '非法操作';
                            that.videoText = '选择视频文件<b class="gray"> 或 </b>拖拽到此处';
                            that.thumbUPlocked = false;
                        }
                    })
                }else{
                    this.videoError = '不是视频文件';
                    this.videoText = '选择视频文件<b class="gray"> 或 </b>拖拽到此处';
                    that.thumbUPlocked = false;
                }
                this.thumbLocked = false;
                this.insertVideoButton = false;
                this.$refs.getVideoFile.value = '';
            }
        },
        table:function(type){
            if(this.thumbLocked == true) return;
            if(type == 'url'){
                this.insertVideoUrl = true;
                this.insertVideoFile = false;
                this.videoError=''
            }else{
                this.insertVideoUrl = false;
                this.insertVideoFile = true;
                this.videoError=''
            }
        },
        dividerHandler:function(){
            var range = this.editor.getSelection(true);
            this.editor.insertText(range.index, '\n', Quill.sources.USER);
            this.editor.insertEmbed(range.index + 1, 'divider', true, Quill.sources.USER);
            this.editor.setSelection(range.index + 2, Quill.sources.SILENT);
        },
        domRest:function(str,type){
            if(type == 'in'){
                var content = ZrzparseHTML(str),
                    out = '';
                for (var i = 0; i < content.length; i++) {
                    //图片
                    if(content[i].className.indexOf('content-img-box') != -1){
                        content[i].innerHTML += '<span class="remove-img-ico click">删除</span>';
                        content[i].setAttribute('contenteditable','false');
                        var des = content[i].querySelectorAll('.addDesn')[0];

                        var input = document.createElement('input');
                                if(des){
                                    input.value = des.innerText;
                                }

                            if(input.value.length > 0){
                                input.className = 'addDesn-input';
                            }else{
                                input.className = 'addDesn-input hide';
                            }
                            content[i].appendChild(input);
                    };

                    //视频
                    if(content[i].className.indexOf('content-video') != -1){
                        content[i].innerHTML += '<span class="remove-img-ico click">删除</span>';
                        content[i].setAttribute('contenteditable','false');
                    }

                    out += content[i].outerHTML;
                }

                return out;
            }else if(type == 'out'){
                str = str.replace(/ contenteditable="false"/g,'').replace(/ contenteditable="true"/g,'')
                .replace(/ placeholder="添加图片注释（可选）"/g,'').replace(/<p><br><\/p>/g,'');
                var content = ZrzparseHTML(str),
                    out = '';
                for (var i = 0; i < content.length; i++) {
                    //删掉删除按钮
                    var rem = content[i].querySelectorAll('.remove-img-ico');
                    if(rem.length > 0){
                        rem[0].remove();
                    }

                    var des = content[i].querySelectorAll('.addDesn-input');
                    if(des.length > 0){
                        des[0].remove();
                    }

                    var img = content[i].querySelectorAll('.po-img-big');
                    if(img.length > 0){
                        img[0].className = 'po-img-big';
                    }

                    var tool = content[i].querySelector('#imgtoolbar');
                    if(tool){
                        tool.remove();
                    }

                    out += content[i].outerHTML;
                }
                return out;
            }
        },
        public:function(type){
            if(this.publicLocked == true || this.success == true) return;
            this.publicLocked = true;
            this.type = type;
            var data = {
                'thumb':this.thumb,
                'title':this.title,
                'content':this.domRest(this.editor.root.innerHTML,'out'),
                'key':this.key,
                'rmb':this.rmb,
                'count':this.count,
                'time':this.time,
                'lv':this.lv,
                'address':this.address,
                'status':type,
                'post_id':activity_script.post_id,
                'filesArg':this.filesArg
            },that = this;

            axios.post(zrz_script.ajax_url+'zrz_public_activity',Qs.stringify(data)).then(function(resout){
                if(resout.data.status == '401'){
                    that.error = resout.data.msg;
                    that.publicLocked = false;
                }else{
                    that.publicLocked = false;
                    that.success = true;
                    that.buttonText = type == 'draft' ? '保存成功' : '成功，跳转中..';
                    window.location.href = resout.data.link;
                };
            })
        }
    }
})

var activitySingle = new Vue({
    el:'.activity-single-header',
    data:{
        show:false,
        userData:{
            'id':zrz_script.current_user,
            'avatar':zrz_script.current_user_data.avatar,
            'name':zrz_script.current_user_data.name
        },
        priceText:'',
        bmdata:{
            'name':'',
            'number':'',
            'sex':1,
            'more':''
        },
        error:'',
        buttonText:'立刻提交',
        submitLocked:false,
        //支付
        showPay:false,
        price:0,
        key:'free',
        showTableText:false,
        login:zrz_script.is_login,
    },
    mounted:function(){
        if(!this.$refs.bmInfo) return;
        this.priceText = activity_single.priceText;
        this.bmdata.number = activity_single.number;
        this.key = activity_single.key;
        console.log(this.key);
        this.price = activity_single.rmb;
    },
    methods:{
        showForm:function(){
            if(!this.login){
                signForm.showBox = true;
                signForm.signin = true;
            }else{
                this.show = true;
            }
        },
        closeForm:function(){
            this.show = false;
            this.submitLocked = false;
            this.buttonText = '立刻提交';
        },
        closePayForm:function(){
            this.showPay = false;
            this.show = false;
            this.submitLocked = false;
            this.buttonText = '立刻提交';
        },
        focusRest:function(){
            this.locked = false;
            this.error = '';
        },
        submit:function(){
            if(this.submitLocked == true) return;

            if(!this.bmdata.name){
                this.error = '请填写姓名';
                return;
            }

            if(!this.bmdata.number){
                this.error = '请填写手机号码';
                return;
            }

            var that = this;
            this.error = '';

            if(this.key == 'rmb'){
                this.buttonText = '跳转中...';
            }else{
                this.buttonText = '提交中...';
            }
           
            this.submitLocked = true;

            axios.post(zrz_script.ajax_url+'zrz_activity_submit',Qs.stringify(this.bmdata)+'&post_id='+activity_single.post_id).then(function(resout){
                console.log(resout);
                if(resout.data.status == 200){
                    that.buttonText = '提交成功';
                    that.show = false;
                    window.location.href = location.href+'?time='+((new Date()).getTime());
                }else if(resout.data.status == 300){
                    that.pay();
                }else{
                    that.buttonText = '重新提交';
                    that.error = resout.data.msg;
                    that.submitLocked = false;
                }
            })
        },
        pay:function(){
            this.show = false;
            this.showPay = true;
        },
    }
})