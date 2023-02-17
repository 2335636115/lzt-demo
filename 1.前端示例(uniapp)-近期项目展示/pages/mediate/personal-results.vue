<style>
    @import "/static/mediate/css/personal-results.css";
    @import "/static/mediate/css/personal-stars.css";
</style>
<template>
    <view>
        <view class="orange-back orange-back-apply">
            <view class="orange-back-information">
                <view class="form-text">调解人信息</view>
                <view class="form-text-back">
                    <view class="form-input">
                        <text class="form-input-text">姓名:</text>
                        <input class="uni-input" :value="name" disabled />
                    </view>
                    <view class="form-input">
                        <text class="form-input-text">电话:</text>
                        <input class="uni-input" :value="mobile" disabled />
                    </view>
                    <view class="form-note-black">*以上信息我们将在系统中严格保密</view>
                </view>
                <view class="form-text">纠纷描述</view>
                <view class="form-text-back">
                    <view class="form-input">
                        <text class="form-input-text">纠纷类型:</text>
                        <input class="uni-input" :value="category" disabled />
                    </view>
                    <view class="form-input">
                        <text class="form-input-text">发生时间:</text>
                        <input class="uni-input" :value="occurred_time" disabled />
                    </view>
                    <view class="form-textarea">
                        <text class="form-input-text">发生地点:</text>
                        <view class="form-input-textarea text-pre">{{occurred_site}}</view>
                        <view class="orange-back-input-border"></view>
                    </view>
                    <view class="form-textarea">
                        <text class="form-input-text">过程描述:</text>
                        <view class="form-input-textarea text-pre">{{description}}</view>
                        <view class="orange-back-input-border"></view>
                    </view>
                    <view class="form-textarea">
                        <text class="form-input-text text-pre">期望结果:</text>
                        <view class="form-input-textarea text-pre">{{outcome}}</view>
                        <view class="orange-back-input-border"></view>
                    </view>
                    <template v-if="attach_file">
                        <view class="form-input" style="min-height:112rpx;height:auto;border:none">
                            <text class="form-input-text">相关资料:</text>
                            <view class="form-input-textarea">
                                <view class="information-detail">
                                    <file-image v-for="item in attach_file" :path="item">
                                        <image class="detail-image" mode="aspectFill" :src="item" :data-id="item" alt="" @click="amplification"></image>
                                    </file-image>
                                </view>
                            </view>
                        </view>
                    </template>
                </view>
                <view v-if="apply_status==90">
                    <view class="form-text">申请已驳回</view>
                    <view class="form-text-back">
                        <view class="form-input">
                            <text class="form-input-text">驳回理由:</text>
                            <view class="form-input-textarea">{{reason}}</view>
                        </view>
                    </view>
                </view>
                <view v-if="workorder">
                    <template v-for="(items,index) in comments" :key="index">
                        <view class="form-text">调解记录{{index+1}}</view>
                        <view class="form-text-back">
                            <view class="form-input">
                                <text class="form-input-text">调解员:</text>
                                <input class="uni-input" :value="workorder.people.username" disabled />
                            </view>
                            <view class="form-input">
                                <text class="form-input-text">调解时间:</text>
                                <input class="uni-input" :value="items.mediate_time" disabled />
                            </view>
                            <view class="form-input">
                                <text class="form-input-text">调解总结:</text>
                                <view class="form-input-textarea">{{items.content}}</view>
                            </view>
                            <view class="form-input" style="min-height:112rpx;height:auto;border:none">
                                <text class="form-input-text">相关资料:</text>
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
                    <view v-if="workorder && workorder.order_status==20">
                        <view v-if="!workorder.evaluate" class="click-evaluation" @click="evaluation">点击评价</view>
                        <view v-else class="have-evaluation">调解已完成，感谢您的评价</view>
                    </view>
                </view>
            </view>
        </view>
        <view class="evaluation-star" :class="specClass">
            <form class="stars" @submit="formSubmit">
                <view class="stars-evaluation">调解评价</view>
                <starsRating :starsIndex="starsIndex" :starsObject="clicked_list" :isEditStars="true" @starsClick="starsClick">
                </starsRating>
                <view class="starsicon-button">
                    <view class="starsicon-button-cancel" @click="cancel">取消</view>
                    <button class="starsicon-button-determine" form-type="submit">确定</button>
                </view>
            </form>
        </view>
        <view :class="amplification_open" @click="close">
            <view class="amplification">
                <image class="amplification-image" mode="aspectFit" :src="amplification_image"></image>
            </view>
        </view>
    </view>
</template>

<script>
    import myFoot from "./footer.vue"
    import starsRating from "./personal-stars.vue"
    import fileImage from "@/components/file-image.vue"

    export default {
        components: {
            myFoot,
            starsRating,
            fileImage
        },
        created() {
            var _this = this
            _this.curShowStars(_this.starsIndex)
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
                apply_status: '',
                reason: '',
                attach_file: '',
                workorder: '',
                attachment: '',
                evaluate: '', //星星评价结果
                starsIndex: 0, // 默认星级评价分数
                clicked_list: {}, //星级评价图标数组
                comments: [],
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
                this.$http.get('mediate/apply_detail', {
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
                    _this.apply_status = result.data.apply_status;
                    _this.reason = result.data.reason;
                    _this.workorder = result.data.workorder;
                    _this.evaluate = result.data.evaluate;
                    if (result.data.attach_file) {
                        _this.attach_file = result.data.attach_file.split("|");
                    }
                    if (result.data.workorder) {
                        if (result.data.workorder.attachment) {
                            _this.attachment = result.data.workorder.attachment.split("|");
                        }
                        _this.comments = result.data.workorder.comments;
                        if (_this.comments) {
                            _this.comments.forEach(function(value, index, array) {
                                if (value.attachment) {
                                    array[index].attachment = value.attachment.split('|');
                                }
                            })
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
            starsClick: function(starsNum) {
                var _this = this
                _this.starsIndex = starsNum
                _this.curShowStars(starsNum)
            },
            // 星星评价展示
            curShowStars: function(starsNum) {
                var _this = this
                if (starsNum == 1) {
                    _this.clicked_list = [
                        "/static/mediate/images/d_star_open.png", "/static/mediate/images/d_star_close.png",
                        "/static/mediate/images/d_star_close.png", "/static/mediate/images/d_star_close.png",
                        "/static/mediate/images/d_star_close.png"
                    ]
                } else if (starsNum == 2) {
                    _this.clicked_list = [
                        "/static/mediate/images/d_star_open.png", "/static/mediate/images/d_star_open.png",
                        "/static/mediate/images/d_star_close.png", "/static/mediate/images/d_star_close.png",
                        "/static/mediate/images/d_star_close.png"
                    ]
                } else if (starsNum == 3) {
                    _this.clicked_list = [
                        "/static/mediate/images/d_star_open.png", "/static/mediate/images/d_star_open.png",
                        "/static/mediate/images/d_star_open.png", "/static/mediate/images/d_star_close.png",
                        "/static/mediate/images/d_star_close.png"
                    ]
                } else if (starsNum == 4) {
                    _this.clicked_list = [
                        "/static/mediate/images/d_star_open.png", "/static/mediate/images/d_star_open.png",
                        "/static/mediate/images/d_star_open.png", "/static/mediate/images/d_star_open.png",
                        "/static/mediate/images/d_star_close.png"
                    ]
                } else if (starsNum == 5) {
                    _this.clicked_list = [
                        "/static/mediate/images/d_star_open.png", "/static/mediate/images/d_star_open.png",
                        "/static/mediate/images/d_star_open.png", "/static/mediate/images/d_star_open.png",
                        "/static/mediate/images/d_star_open.png"
                    ]
                } else {
                    _this.clicked_list = [
                        "/static/mediate/images/d_star_close.png", "/static/mediate/images/d_star_close.png",
                        "/static/mediate/images/d_star_close.png", "/static/mediate/images/d_star_close.png",
                        "/static/mediate/images/d_star_close.png"
                    ]
                    _this.starsIndex = 0
                }
            },
            evaluation: function() {
                this.specClass = "show";
            },
            cancel: function() {
                this.specClass = "hide";
            },
            formSubmit: function(e) {
                var _this = this;
                let local = location.href;
                let id = this.getParam(local, "id");
                if (this.starsIndex > 0) {
                    this.$http.post('mediate/evaluate', {
                        id: id,
                        score: this.starsIndex
                    }, result => {
                        if (result.code == 1) {
                            uni.showToast({
                                title: '评价成功',
                                icon: 'success',
                                duration: 2000
                            });
                        }
                        this.specClass = "hide";
                        location.reload();
                    })
                } else {
                    uni.showToast({
                        title: '请点击星星评分',
                        icon: 'error',
                        duration: 2000
                    });
                }
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
