<style>
    @import "/static/mediate/css/case-detail-ing.css";
</style>
<template>
    <view>
        <view class="orange-back orange-back-review">
            <view class="orange-back-information">
                <view class="form-text">申请人信息</view>
                <view class="form-text-back">
                    <view class="form-input">
                        <text class="form-input-text">姓名：</text>
                        <input class="uni-input" :value="name" disabled />
                    </view>
                    <view class="form-input">
                        <text class="form-input-text">电话：</text>
                        <input class="uni-input" :value="mobile" disabled />
                    </view>
                    <view class="form-input">
                        <text class="form-input-text">身份证号：</text>
                        <input class="uni-input" :value="idcard" disabled />
                    </view>
                </view>
                <view class="form-text">纠纷描述</view>
                <view class="form-text-back">
                    <view class="form-input">
                        <text class="form-input-text">纠纷类型：</text>
                        <input class="uni-input" :value="category" disabled />
                    </view>
                    <view class="form-input">
                        <text class="form-input-text">发生时间：</text>
                        <input class="uni-input" :value="occurred_time" disabled />
                    </view>
                    <view class="form-textarea">
                        <text class="form-input-text">发生地点：</text>
                        <view class="form-input-textarea">{{occurred_site}}</view>
                        <view class="orange-back-input-border"></view>
                    </view>
                    <view class="form-textarea">
                        <text class="form-input-text">过程描述：</text>
                        <view class="form-input-textarea">{{description}}</view>
                        <view class="orange-back-input-border"></view>
                    </view>
                    <view class="form-textarea">
                        <text class="form-input-text">期望结果：</text>
                        <view class="form-input-textarea">{{outcome}}</view>
                        <view class="orange-back-input-border"></view>
                    </view>
                    <view class="form-input" style="min-height:112rpx;height:auto;border:none">
                        <text class="form-input-text">相关资料：</text>
                        <view class="form-input-textarea">
                            <view class="information-detail">
                                <file-image v-for="item in attach_file" :path="item">
                                    <image class="detail-image" mode="aspectFill" :src="item" :data-id="item" alt="" @click="amplification"></image>
                                </file-image>
                            </view>
                        </view>
                    </view>
                </view>
                <template v-for="(items,index) in comments" :key="index">
                    <view class="form-text">调解记录{{index+1}}</view>
                    <view class="form-text-back">
                        <view class="form-input">
                            <text class="form-input-text">处理时间：</text>
                            <view class="form-input-textarea">{{items.mediate_time}}</view>
                        </view>
                        <view class="form-input">
                            <text class="form-input-text">调解记录：</text>
                            <view class="form-input-textarea">{{items.content}}</view>
                        </view>
                        <view class="form-input" style="min-height:112rpx;height:auto;border:none">
                            <text class="form-input-text">相关资料：</text>
                            <view class="form-input-textarea">
                                <view class="information-detail">
                                    <file-image v-for="item in items.attachment" :path="item">
                                        <image class="detail-image" mode="aspectFill" :src="item" :data-id="item" alt="" @click="amplification"></image>
                                    </file-image>
                                </view>
                            </view>
                        </view>
                    </view>
                </template>
                <view class="form-text">调解记录</view>
                <form @submit="submit">
                    <view class="form-text-back">
                        <view class="form-textarea">
                            <text class="form-input-text">事件评述：</text>
                            <textarea class="form-input-textarea" name="content" @input="inputcontent" placeholder-style="color:#4F4F4F" placeholder="请填写" auto-height />
                            <view class="orange-back-input-border"></view>
                        </view>
                        <view class="form-textarea">
                            <text class="form-input-text">资料上传：</text>
                            <view class="form-upload-img">
                                <uni-file-picker class="choosePicture" limit="4" file-mediatype="all" title="" mode="grid" @select="select" @delete="deletefile">
                                </uni-file-picker>
                            </view>
                            <view class="orange-back-input-border"></view>
                        </view>
                        <view class="form-input">
                            <text class="form-input-text">调解状态：</text>
                            <view class="form-input-textarea">
                                <radio-group class="information-list list-radio" @change="radioChanges" style="border:none">
                                    <label class="uni-list-cell uni-list-cell-pd" v-for="(item, index) in mediate_status" :key="item.value" v-show="index!=2">
                                        <view>
                                            <radio :value="item.value" color="#ec6618" :checked="index === currents" />
                                            {{item.name}}
                                        </view>
                                    </label>
                                </radio-group>
                            </view>
                        </view>
                        <view class="form-input" style="border:none">
                            <text class="form-input-text">需要回访：</text>
                            <view class="form-input-textarea">
                                <radio-group class="information-list list-radio" @change="radioChange" style="border:none">
                                    <label class="uni-list-cell uni-list-cell-pd" v-for="(item, index) in radioItems" :key="item.value">
                                        <view>
                                            <radio :value="item.value" :checked="index === current" />
                                            {{item.name}}
                                        </view>
                                    </label>
                                </radio-group>
                            </view>
                        </view>
                    </view>
                    <button form-type="submit" class="form-button" style="margin-top:70rpx;">确认</button>
                </form>
            </view>
        </view>
        <view :class="amplification_open" @click="close">
            <view class="amplification">
                <image class="amplification-image" mode="aspectFit" :src="amplification_image"></image>
            </view>
        </view>
        <view class="status-success" :class="specClass">
            <view class="success-tip">
                <view class="success-tip-img">
                    <image class="success-tip-image" src="/static/mediate/images/c-012.png" mode=""></image>
                </view>
                <view class="success-tip-title">上传成功</view>
                <view class="success-tip-content">已完成调解，资料已上传成功！</view>
                <button class="success-tip-button" @click="close_tip_a">确定</button>
            </view>
        </view>
        <view class="status-success" :class="specClassb">
            <view class="success-tip">
                <view class="success-tip-img">
                    <image class="success-tip-image" src="/static/mediate/images/c-012.png" mode=""></image>
                </view>
                <view class="success-tip-title">提交成功</view>
                <button class="success-tip-button" @click="close_tip_b">确定</button>
            </view>
        </view>
    </view>
</template>

<script>
    import request from '../../common/js/mediator-request.js';
    import myHead from "./header.vue"
    import myFoot from "./footer.vue"
    import fileUpload from "../mediate/apply-file-upload.vue"
    import fileImage from "@/components/file-image.vue"

    export default {
        components: {
            myHead,
            myFoot,
            fileUpload,
            fileImage
        },
        data() {
            return {
                object_news: [],
                name: '',
                mobile: '',
                idcard: '',
                category: '',
                occurred_time: '',
                occurred_site: '',
                description: '',
                outcome: '',
                attach_file: '',
                workorder_id: '',
                radioItems: [{
                        value: '0',
                        name: '否',
                        checked: 'true'
                    },
                    {
                        value: '1',
                        name: '是',
                    },
                ],
                mediate_status: [{
                        value: '0',
                        name: '失败',
                        checked: 'true'
                    },
                    {
                        value: '1',
                        name: '成功',
                    },
                    {
                        value: '2',
                        name: '跟进中',
                    },
                    {
                        value: '3',
                        name: '不予受理',
                    },
                ],
                current: 0,
                currents: 0,
                comments: '',
                amplification_image: '',
                amplification_open: 'hide',
                content: '',
                specClass: 'hide',
                specClassb: 'hide',
            }
        },
        created() {
            this.date = this.getDate({
                format: true
            })
        },
        computed: {
            startDate() {
                return this.getDate('start');
            },
            endDate() {
                return this.getDate('end');
            }
        },
        onShow() {
            this.getdetail();
        },
        methods: {
            inputcontent(e) {
                if (e) {
                    this.content = e.detail.value;
                }
            },
            getdetail() {
                var _this = this;
                let local = location.href;
                let id = this.getParam(local, "id");
                request.get('mediator/detail', {
                    id: id
                }, result => {
                    _this.workorder_id = result.data.workorder.id;
                    _this.name = result.data.name;
                    _this.mobile = result.data.mobile;
                    _this.idcard = result.data.idcard;
                    _this.category = result.data.category.name;
                    _this.occurred_time = result.data.occurred_time;
                    _this.occurred_site = result.data.occurred_site;
                    _this.description = result.data.description;
                    _this.outcome = result.data.outcome;
                    _this.current = result.data.workorder.is_need_visit;
                    _this.currents = result.data.workorder.mediate_status;
                    if (result.data.attach_file) {
                        _this.attach_file = result.data.attach_file;
                        const attach_file = _this.attach_file;
                        const attach_files = attach_file.split("|");
                        _this.attach_file = attach_files;
                    }
                    this.getcomments();
                })
            },
            getcomments() {
                var _this = this;
                let id = _this.workorder_id;
                request.get('mediator/comments', {
                    id: id
                }, result => {
                    _this.comments = result.data.comments;
                    let thiscomments = _this.comments;
                    for (let i = 0; i < thiscomments.length; i++) {
                        let i_attachment = thiscomments[i].attachment;
                        let attachments = [];
                        if (i_attachment) {
                            let attachments = i_attachment.split('|');
                            thiscomments[i].attachment = attachments
                        }
                    }
                })
            },
            getParam(path, name) {
                var reg = new RegExp("(^|\\?|&)" + name + "=([^&]*)(\\s|&|$)", "i");
                if (reg.test(path))
                    return unescape(RegExp.$2.replace(/\+/g, " "));
                return "";
            },
            submit: function(e) {
                var formdata = e.detail.value;
                const file_all = this.object_news;
                var files = "";
                for (let a in file_all) {
                    if (!files) {
                        files = files + file_all[a];
                    } else {
                        files = files + "|" + file_all[a];
                    }
                }
                request.post('mediator/edit', {
                    id: this.workorder_id,
                    comment: this.content,
                    attachment: files,
                    is_need_visit: this.current,
                    mediate_status: this.currents
                }, result => {
                    if (result.code == 1) {
                        if (this.currents == 2) {
                            this.specClassb = "show"; //跟进中
                        } else {
                            this.specClass = "show"; //成功
                        }
                    }
                })
            },
            // 成功 => 回访
            close_tip_a: function(e) {
                let local = location.href;
                let id = this.getParam(local, "id");
                uni.redirectTo({
                    url: '/pages/mediator/mine-record?id=' + id,
                });
            },
            // 跟进中 => 原页
            close_tip_b: function(e) {
                let local = location.href;
                let id = this.getParam(local, "id");
                uni.redirectTo({
                    url: '/pages/mediator/case-detail-review?id=' + id,
                });
            },
            bindDateChange: function(e) {
                this.date = e.detail.value
            },
            getDate(type) {
                const date = new Date();
                let year = date.getFullYear();
                let month = date.getMonth() + 1;
                let day = date.getDate();

                if (type === 'start') {
                    year = year - 20;
                } else if (type === 'end') {
                    year = year + 2;
                }
                month = month > 9 ? month : '0' + month;
                day = day > 9 ? day : '0' + day;
                return `${year}-${month}-${day}`;
            },
            downloadfile: function(e) {
                uni.downloadFile({
                    url: e.currentTarget.dataset.id, //仅为示例，并非真实的资源
                    success: (res) => {
                        if (res.statusCode === 200) {
                            console.log('下载成功');
                        }
                    }
                });
            },
            // 获取上传状态
            select(e) {
                let obj = {};
                let object_news = this.object_news;
                e.tempFilePaths.forEach((val, ind) => {
                    request.upload('mediator/upload', val, {}, res => {
                        const url = res.data.url; //上传文件位置	
                        obj[e.tempFiles[ind].uuid] = url;
                        const obj_all = Object.assign(obj, object_news);
                        this.object_news = obj_all;
                    })
                })
            },
            // 获取上传进度
            progress(e) {
                console.log('上传进度：', e)
            },
            // 上传成功
            success(e) {
                console.log('上传成功')
            },
            // 上传失败
            fail(e) {
                console.log('上传失败：', e)
            },
            //文件从列表移除时触发
            deletefile(e) {
                const i = e.tempFile.uuid;
                const object_all = this.object_news;
                Reflect.deleteProperty(object_all, i)
                this.object_news = object_all;
            },
            amplification: function(e) {
                this.amplification_open = 'show';
                this.amplification_image = e.currentTarget.dataset.id;
            },
            close: function(e) {
                this.amplification_open = 'hide';
            },
            radioChange: function(evt) {
                for (let i = 0; i < this.radioItems.length; i++) {
                    if (this.radioItems[i].value === evt.detail.value) {
                        this.current = i;
                    }
                }
            },
            radioChanges: function(evt) {
                for (let i = 0; i < this.mediate_status.length; i++) {
                    if (this.mediate_status[i].value === evt.detail.value) {
                        this.currents = i;
                    }
                }
            }
        }
    }
</script>
