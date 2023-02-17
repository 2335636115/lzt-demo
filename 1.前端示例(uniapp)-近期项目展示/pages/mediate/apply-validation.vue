<style>
    @import "/static/mediate/css/apply-index.css";
</style>
<template>
    <view>
        <view class="orange-back orange-back-apply">
            <form class="orange-back-form" @submit="submit">
                <view class="form-text-back form-instructions">
                    <view class="form-i-txt">
                        <view>为保证调解工作可以顺利进行</view>
                        <view>我们需要验证您的真实身份</view>
                    </view>
                </view>
                <view class="form-text-back">
                    <view class="form-input">
                        <text class="form-input-text">姓名:</text>
                        <input class="uni-input" @input="inputname" v-model="name" placeholder="请填写" maxlength="15" />
                    </view>
                    <view class="form-input">
                        <text class="form-input-text">身份证号:</text>
                        <input class="uni-input" @input="inputcard" v-model="idcard" placeholder="请填写" maxlength="20" />
                    </view>
                    <view class="form-input form-textarea">
                        <text class="form-input-text">身份证正反面:</text>
                        <view class="form-upload-imgs form-white">
                            <view class="card-img">
                                <uni-section title="" type="line">
                                    <uni-file-picker limit="1" title="" @select="select_front" @delete="delete_front">
                                    </uni-file-picker>
                                </uni-section>
                                <view class="line-view">正面</view>
                            </view>
                            <view class="card-img">
                                <uni-section title="" type="line">
                                    <uni-file-picker limit="1" title="" @select="select_back" @delete="delete_back">
                                    </uni-file-picker>
                                </uni-section>
                                <view class="line-view">反面</view>
                            </view>
                        </view>
                    </view>
                </view>
                <view class="form-note">*以上信息我们将在系统严格保密</view>
                <button :class="specClass" :disabled="isDisable" @click="submit">下一步</button>
            </form>
        </view>
    </view>
</template>

<script>
    import myFoot from "./footer.vue"

    export default {
        components: {
            myFoot,
        },
        data() {
            return {
                name: '',
                idcard: '',
                object_news_front: '',
                object_news_back: '',

                isDisable: !1,
            }
        },
        computed: {
            specClass() {
                return this.name == '' || this.idcard == '' || this.object_news_front == '' || this.object_news_back ==
                    '' ? 'form-button form-button-light' : 'form-button';
            }
        },
        onShow() {

        },
        methods: {
            inputname(e) {
                this.name = e.detail.value;
            },
            inputcard(e) {
                setTimeout(() => {
                    this.idcard = e.detail.value.replace(/[^\dx]/ig, "");
                }, 30)
            },
            submit: function(e) {
                var that = this;
                if ((this.name) && (this.idcard) && (this.object_news_front) && (this.object_news_back)) {
                    const idcard =
                        /^[1-9]\d{5}(18|19|20|(3\d))\d{2}((0[1-9])|(1[0-2]))(([0-2][1-9])|10|20|30|31)\d{3}[0-9Xx]$/
                        .test(this.idcard);
                    if (!idcard) {
                        this.showMsg('身份证不正确');
                        return;
                    }
                    that.isDisable = !0;
                    this.$http.post('mediate/user_save', {
                        username: this.name,
                        idcard: this.idcard,
                        idcard_front: this.object_news_front,
                        idcard_back: this.object_news_back,
                    }, result => {
                        if (result.code == 1) {
                            uni.redirectTo({
                                url: '/pages/mediate/apply-index'
                            });
                        }
                    }, res => {}, res => {
                        that.isDisable = !1;
                    })
                }
            },
            // 获取上传状态（身份证前）
            select_front(e) {
                e.tempFilePaths.forEach((val, ind) => {
                    this.$http.upload('index/upload', val, {}, res => {
                        const url = res.data.url; //上传文件位置	
                        this.object_news_front = url;
                    })
                })
            },
            // 获取上传状态（身份证后）
            select_back(e) {
                e.tempFilePaths.forEach((val, ind) => {
                    this.$http.upload('index/upload', val, {}, res => {
                        const url = res.data.url; //上传文件位置	
                        this.object_news_back = url;
                    })
                })
            },
            //删除（身份证前）
            delete_front(e) {
                this.object_news_front = '';
            },
            //删除（身份证后）
            delete_back(e) {
                this.object_news_back = '';
            },
            showMsg: function(msg) {
                uni.showToast({
                    title: msg,
                    icon: 'error',
                    duration: 2000
                });
            }
        }
    }
</script>
