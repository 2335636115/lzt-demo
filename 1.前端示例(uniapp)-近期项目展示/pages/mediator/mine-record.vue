<style>
    @import "/static/mediate/css/mine-record.css";
</style>
<template>
    <view>
        <form class="orange-back">
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
                                        <image class="detail-image" mode="aspectFill" :src="item" :data-id="item" @click="amplification"></image>
                                    </file-image>
                                </view>
                            </view>
                        </view>
                    </view>
                </template>
                <view class="form-text">纠纷已调解完成</view>
                <view class="form-text-back">
                    <view class="form-input">
                        <text class="form-input-text">调解状况：</text>
                        <template v-if="mediate_status == 0">
                            <input class="uni-input" value="失败" disabled />
                        </template>
                        <template v-if="mediate_status == 1">
                            <input class="uni-input" value="成功" disabled />
                        </template>
                        <template v-if="mediate_status == 2">
                            <input class="uni-input" value="跟进中" disabled />
                        </template>
                        <template v-if="mediate_status == 3">
                            <input class="uni-input" value="不予受理" disabled />
                        </template>
                    </view>
                    <view class="form-textarea">
                        <text class="form-input-text">事件评述：</text>
                        <view class="form-input-textarea">{{comment}}</view>
                        <view class="orange-back-input-border"></view>
                    </view>
                    <view class="form-input" style="min-height:112rpx;height:auto;">
                        <text class="form-input-text">相关资料：</text>
                        <view class="form-input-textarea">
                            <view class="information-detail">
                                <file-image v-for="item in attachment" :path="item">
                                    <image class="detail-image" mode="aspectFill" :src="item" :data-id="item" @click="amplification"></image>
                                </file-image>
                            </view>
                        </view>
                    </view>
                    <template v-if="visitlog">
                        <view class="form-input" style="border:none">
                            <text class="form-input-text">回访记录：</text>
                            <view class="form-input-textarea">{{visitlog_content}}</view>
                        </view>
                    </template>
                </view>
                <template v-if="evaluate">
                    <view class="have-evaluation">调解已完成，已评价（{{evaluate}}星）</view>
                </template>
                <template v-if="is_need_visit == 1">
                    <view class="form-button" v-if="!visitlog" @click="jump">填写回访记录</view>
                </template>
                <view class="blank"></view>
            </view>
        </form>
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
                name: '',
                mobile: '',
                idcard: '',
                category: '',
                occurred_time: '',
                occurred_site: '',
                description: '',
                outcome: '',
                comment: '',
                attach_file: [],
                attachment: [],
                visitlog: '',
                workorder_id: '',
                visitlog_content: '',
                mediate_status: '',
                is_need_visit: '',
                evaluate: '',
                amplification_image: '',
                amplification_open: 'hide',
                comments: '',
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
                    _this.name = result.data.name;
                    _this.mobile = result.data.mobile;
                    _this.idcard = result.data.idcard;
                    _this.category = result.data.category.name;
                    _this.occurred_time = result.data.occurred_time;
                    _this.occurred_site = result.data.occurred_site;
                    _this.description = result.data.description;
                    _this.outcome = result.data.outcome;
                    _this.workorder_id = result.data.workorder.id;
                    _this.comment = result.data.workorder.comment;
                    _this.mediate_status = result.data.workorder.mediate_status;
                    _this.is_need_visit = result.data.workorder.is_need_visit;
                    if (result.data.workorder.evaluate) {
                        _this.evaluate = result.data.workorder.evaluate.score;
                    };
                    if (result.data.attach_file) {
                        _this.attach_file = result.data.attach_file;
                        const attach_file = _this.attach_file;
                        const attach_files = attach_file.split("|");
                        _this.attach_file = attach_files;
                    }
                    if (result.data.workorder.attachment) {
                        _this.attachment = result.data.workorder.attachment;
                        const attachment = _this.attachment;
                        const attachments = attachment.split("|");
                        _this.attachment = attachments;
                    }
                    if (result.data.workorder.visitlog) {
                        this.visitlog = 1;
                        this.visitlog_content = result.data.workorder.visitlog.content;
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
            jump: function(e) {
                let local = location.href;
                let id = this.getParam(local, "id");
                uni.redirectTo({
                    url: '/pages/mediator/mine-conclusion?id=' + id + "&workorder_id=" + this.workorder_id,
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
