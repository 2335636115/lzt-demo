<style>
    @import "/static/mediate/css/case-detail-ing.css";
</style>
<template>
    <view>
        <view class="orange-back orange-back-apply">
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
                        <view class="form-input">
                            <text class="form-input-text">处理时间：</text>
                            <uni-datetime-picker v-model="date" :start="startDate" :end="endDate" @change="bindDateChange" style="position:relative;">
                                <view class="uni-input">{{ mediate_time }}</view>
                                <uni-icons type="forward" size="16" color="#9a9a9a" class="icons"></uni-icons>
                            </uni-datetime-picker>
                        </view>
                        <view class="form-textarea">
                            <text class="form-input-text">调解记录：</text>
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
                    </view>
                    <button form-type="submit" class="form-button" :disabled="isDisable" v-if="canSubmit">确认</button>
                    <button class="form-button form-button-light" v-else>确认</button>
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
                <view class="success-tip-title">提交成功</view>
                <button class="success-tip-button" @click="close_tip">确定</button>
            </view>
        </view>
    </view>
</template>

<script>
    import request from '../../common/js/mediator-request.js';
    import myHead from "./header.vue"
    import myFoot from "./footer.vue"
    import fileImage from "@/components/file-image.vue"

    export default {
        components: {
            myHead,
            myFoot,
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
                mediate_status: [{
                    value: '0',
                    name: '失败',
                    checked: 'true'
                }, {
                    value: '1',
                    name: '成功',
                }, {
                    value: '2',
                    name: '跟进中',
                }, {
                    value: '3',
                    name: '不予受理',
                }],
                current: 0,
                currents: 0,
                comments: '',
                amplification_image: '',
                amplification_open: 'hide',
                content: '',
                specClass: 'hide',
                mediate_time: '',

                isDisable: !1, // 按钮disabled状态
                uploadFileArr: {}, // 文件上传状态数组
                uploadLoad: !1, // 文件总上传状态
            }
        },
        created() {
            this.date = this.getDate({
                format: true
            });
            this.mediate_time = this.date;
        },
        computed: {
            startDate() {
                return this.getDate('start');
            },
            endDate() {
                return this.getDate('end');
            },
            canSubmit() {
                return this.content == '' || this.uploadLoad ? false : true;
            }
        },
        onShow() {
            this.getdetail();
        },
        watch: {
            uploadFileArr: {
                handler: function(newName, oldName) {
                    let arr = Object.values(newName)
                    this.uploadLoad = arr.indexOf('load') > -1;
                },
                deep: true
            }
        },
        methods: {
            inputcontent(e) {
                this.content = e.detail.value;
            },
            getdetail() {
                let _this = this;
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
                        _this.attach_file = result.data.attach_file.split("|");
                    }
                    this.getcomments();
                })
            },
            getcomments() {
                let _this = this;
                let id = _this.workorder_id;
                request.get('mediator/comments', {
                    id: id
                }, result => {
                    _this.comments = result.data.comments;
                    if (_this.comments) {
                        _this.comments.forEach(function(value, index, array) {
                            if (value.attachment) {
                                array[index].attachment = value.attachment.split('|');
                            }
                        })
                    }
                })
            },
            getParam(path, name) {
                let reg = new RegExp("(^|\\?|&)" + name + "=([^&]*)(\\s|&|$)", "i");
                if (reg.test(path))
                    return unescape(RegExp.$2.replace(/\+/g, " "));
                return "";
            },
            submit: function(e) {
                let that = this;
                if (that.content) {
                    that.isDisable = !0
                    let formdata = e.detail.value;
                    let file_all = that.object_news;
                    let files = "";
                    for (let a in file_all) {
                        if (!files) {
                            files = files + file_all[a];
                        } else {
                            files = files + "|" + file_all[a];
                        }
                    }
                    request.post('mediator/comment_save', {
                        id: that.workorder_id,
                        mediate_time: that.mediate_time,
                        content: formdata.content,
                        attachment: files,
                    }, result => {
                        if (result.code == 1) {
                            that.specClass = "show"; //成功
                        }
                    }, res => {

                    }, res => {
                        that.isDisable = !1;
                    })
                }
            },
            bindDateChange: function(e) {
                this.date = e
                this.mediate_time = this.date
            },
            getDate(type) {
                const date = new Date();
                let year = date.getFullYear();
                let month = date.getMonth() + 1;
                let day = date.getDate();
                let hour = date.getHours();
                let minute = date.getMinutes();
                let second = date.getSeconds();

                if (type === 'start') {
                    year = year - 2;
                } else if (type === 'end') {
                    year = year + 2;
                }
                month = month > 9 ? month : '0' + month;
                day = day > 9 ? day : '0' + day;
                hour = hour > 9 ? hour : '0' + hour;
                minute = minute > 9 ? minute : '0' + minute;
                second = second > 9 ? second : '0' + second;
                return `${year}-${month}-${day} ${hour}:${minute}:${second}`;
            },
            // 跟进中 => 原页
            close_tip: function(e) {
                let local = location.href;
                let id = this.getParam(local, "id");
                uni.redirectTo({
                    url: '/pages/mediator/case-detail-ing?id=' + id,
                });
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
                let _this = this;
                let obj = {};
                let object_news = this.object_news;
                e.tempFilePaths.forEach((val, ind) => {
                    _this.uploadFileArr[e.tempFiles[ind].uuid] = 'load';
                    request.upload('mediator/upload', val, {}, res => {
                        obj[e.tempFiles[ind].uuid] = res.data.url; //上传文件位置    
                        this.object_news = Object.assign(obj, object_news);
                        _this.uploadFileArr[e.tempFiles[ind].uuid] = 'success';
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
            }
        }
    }
</script>
