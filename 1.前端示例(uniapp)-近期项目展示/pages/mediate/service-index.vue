<style>
    @import "/static/mediate/css/service-index.css";
</style>
<template>
    <view>
        <view class="orange-back">
            <view class="orange-back-swiper">
                <view class="uni-margin-wrap">
                    <swiper class="swiper swiper-index" circular :indicator-dots="false" :autoplay="true" :interval="4500" :duration="1000" :vertical="false">
                        <swiper-item>
                            <view class="swiper-item uni-bg-red">
                                <image class="orange-back-swiper-image" src="/static/mediate/images/banner001.jpg"></image>
                            </view>
                        </swiper-item>
                    </swiper>
                </view>
            </view>
        </view>
        <view class="services-menu">
            <navigator class="services-menu-list" @click="apply">
                <view class="services-menu-img">
                    <image class="services-menu-image" src="/static/mediate/images/b-002.png" alt=""></image>
                </view>
                <view class="services-menu-content">
                    <view class="services-menu-title">调解申请</view>
                </view>
            </navigator>
            <!-- <navigator class="services-menu-list" url="/pages/mediate/mediator-list">
				<view class="services-menu-img">
					<image class="services-menu-image" src="/static/mediate/images/b-007.png" alt=""></image>
				</view>
				<view class="services-menu-content">
					<view class="services-menu-title">调解员公示</view>
				</view>
			</navigator> -->
            <navigator class="services-menu-list" url="/pages/mediate/personal-index">
                <view class="services-menu-img">
                    <image class="services-menu-image" src="/static/mediate/images/b-004.png" alt=""></image>
                </view>
                <view class="services-menu-content">
                    <view class="services-menu-title">调解查询</view>
                </view>
            </navigator>
            <navigator class="services-menu-list" url="/pages/mediate/law-list">
                <view class="services-menu-img">
                    <image class="services-menu-image" src="/static/mediate/images/b-005.png" alt=""></image>
                </view>
                <view class="services-menu-content">
                    <view class="services-menu-title">法律法规</view>
                </view>
            </navigator>
            <navigator class="services-menu-list" url="/pages/mediate/chat-index">
                <view class="services-menu-img">
                    <image class="services-menu-image" src="/static/mediate/images/a-011b.png" alt=""></image>
                </view>
                <view class="services-menu-content">
                    <view class="services-menu-title">在线客服</view>
                </view>
            </navigator>
            <navigator class="services-menu-list" url="/pages/mediator/login-index">
                <view class="services-menu-img">
                    <image class="services-menu-image" src="/static/mediate/images/b-008.png" alt=""></image>
                </view>
                <view class="services-menu-content">
                    <view class="services-menu-title">调解员登录</view>
                </view>
            </navigator>
        </view>
        <myFoot></myFoot>
    </view>
</template>

<script>
    import myFoot from "./footer.vue"

    export default {
        components: {
            myFoot
        },
        data() {
            return {
                is_perfect: 0,
            }
        },
        onShow() {
            uni.setStorageSync('footIndex', 1);
            this.homepage();
        },
        methods: {
            homepage() {
                var _this = this;
                this.$http.get('mediate/index', {

                }, result => {
                    this.is_perfect = result.data.is_perfect;
                })
            },
            apply: function(e) {
                if (this.is_perfect == 0) {
                    uni.navigateTo({
                        url: '/pages/mediate/apply-validation'
                    });
                } else {
                    uni.navigateTo({
                        url: '/pages/mediate/apply-index'
                    });
                }
            }
        }
    }
</script>

<style>

</style>
