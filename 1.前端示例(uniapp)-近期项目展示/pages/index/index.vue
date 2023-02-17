<template>
    <template v-if="dev_env=='development'">
        <view class="container">
            <form @submit="formSubmit" class="uni-common-mt">
                <view class="uni-form-item uni-column" style="margin-top:20%;"></view>
                <view class="uni-form-item uni-column">
                    <view class="uni-input-wrapper"><input class="uni-input" name="mobile" @input="mobileInput" placeholder="请输入手机号" /></view>
                </view>
                <view class="uni-form-item uni-column">
                    <view class="uni-input-wrapper"><input class="uni-input" name="code" placeholder="请输入验证码" /></view>
                </view>
                <view class="uni-btn-v">
                    <button type="default" @click="getCode">获取验证码</button>
                </view>
                <view class="uni-btn-v">
                    <button form-type="submit" type="primary">用户登录</button>
                </view>
            </form>
        </view>
    </template>
    <template v-else>
        <view class="info-box">
            <view>
                <uni-icons type="info-filled" color="#2979ff" size="120"></uni-icons>
            </view>
            <view>请在住这儿小程序中打开</view>
        </view>
    </template>
</template>

<script>
    import util from '@/common/js/util.js';
    export default {
        data() {
            return {
                mobile: '',
                dev_env: process.env.NODE_ENV
            }
        },
        methods: {
            login: function(param) {
                this.$http.post('index/login', param, result => {
                    // 记录token user_id
                    uni.setStorageSync('token', result.data.token);
                    uni.setStorageSync('user_id', result.data.user_id);
                    uni.reLaunch({
                        url: "/pages/mediate/service-index"
                    });
                });
            },
            formSubmit: function(e) {
                let data = e.detail.value;
                if (!data.mobile || data.mobile == '') {
                    util.showError('请输入手机号码');
                    return false;
                }
                if (!data.code || data.code == '') {
                    util.showError('请输入验证码');
                    return false;
                }
                this.login({
                    mobile: data.mobile,
                    code: data.code,
                    user_info: JSON.stringify({
                        mobile: data.mobile
                    })
                });
            },
            mobileInput: function(e) {
                this.mobile = e.detail.value;
            },
            getCode: function(e) {
                var _this = this;
                if (!_this.mobile || _this.mobile.length != 11) {
                    util.showError('手机号码不正确');
                    return false;
                }
                _this.$http.post('index/getcode', {
                    mobile: _this.mobile
                }, result => {
                    util.showSuccess(result.data.toString());
                })
            }
        }
    }
</script>

<style>
    .container {
        padding: 20px;
        font-size: 14px;
        line-height: 24px;
    }

    .info-box {
        padding: 150rpx 30rpx;
        text-align: center;
        font-size: 40rpx;
        line-height: 1.2;
    }

    .uni-input-form,
    .uni-input-wrapper {
        display: -webkit-box;
        display: -webkit-flex;
        display: flex;
        position: relative;
        width: 100%;
        height: 100%;
        -webkit-box-orient: vertical;
        -webkit-box-direction: normal;
        -webkit-flex-direction: column;
        flex-direction: column;
        -webkit-box-pack: center;
        -webkit-justify-content: center;
        justify-content: center;
    }

    .uni-form-item {
        display: flex;
        width: 100%;
        padding: 5px 0;
    }

    .uni-input {
        height: 28px;
        line-height: 28px;
        font-size: 15px;
        padding: 0;
        flex: 1;
        background-color: #fff;
    }

    .uni-input-wrapper {
        display: flex;
        padding: 8px 13px;
        flex-direction: row;
        flex-wrap: nowrap;
        background-color: #fff;
    }

    .uni-btn-v {
        margin-top: 15px;
        margin-bottom: 15px;
    }
</style>
