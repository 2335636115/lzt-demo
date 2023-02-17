<style>
    @import "/static/mediate/css/case-detail-miss.css";
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
                        <view class="orange-back-input-border"></view>
                    </view>
                    <view class="form-textarea">
                        <text class="form-input-text">后台备注：</text>
                        <view class="form-input-textarea">{{remark}}</view>
                    </view>
                </view>
            </view>
            <button class="form-button" @click="formSubmit">受理</button>
        </view>

        <view class="status-success" :class="specClass">
            <view class="success-tip">
                <view class="success-tip-img">
                    <image class="success-tip-image" src="/static/mediate/images/c-012.png" mode=""></image>
                </view>
                <view class="success-tip-title">受理成功</view>
                <view class="success-tip-content">调解申请已受理成功，请尽快安排跟进！</view>
                <button class="success-tip-button" @click="close_tip">确定</button>
            </view>
        </view>
        <view :class="amplification_open" @click="close">
            <view class="amplification">
                <image class="amplification-image" mode="aspectFit" :src="amplification_image"></image>
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
            fileImage,
        },
        data() {
            return {
                specClass: 'hide',
                name: '',
                mobile: '',
                idcard: '',
                category: '',
                occurred_time: '',
                occurred_site: '',
                description: '',
                outcome: '',
                remark: '',
                attach_file: '',
                workorder_id: '',
                amplification_image: '',
                amplification_open: 'hide'
            }
        },
        onShow() {
            this.getdetail();
        },
        methods: {
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
                    _this.remark = result.data.workorder.remark;
                    if (result.data.attach_file) {
                        _this.attach_file = result.data.attach_file;
                        const attach_file = _this.attach_file;
                        const attach_files = attach_file.split("|");
                        _this.attach_file = attach_files;
                    }
                })
            },
            getParam(path, name) {
                var reg = new RegExp("(^|\\?|&)" + name + "=([^&]*)(\\s|&|$)", "i");
                if (reg.test(path))
                    return unescape(RegExp.$2.replace(/\+/g, " "));
                return "";
            },
            formSubmit: function(e) {
                request.post('mediator/accept', {
                    id: this.workorder_id
                }, result => {
                    if (result.code == 1) {
                        this.specClass = "show";
                    }
                })
            },
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
