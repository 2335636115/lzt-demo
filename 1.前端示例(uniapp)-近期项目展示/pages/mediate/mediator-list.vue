<style>
    @import "/static/mediate/css/service-index.css";
    @import "/static/mediate/css/mediator-index.css";
</style>
<template>
    <view>
        <scroll-view class="scrollview" @scrolltolower="bindDownLoad" scroll-y="true" :style="{height:phoneHeight}">
            <view class="box-bg head-title">
                <uni-nav-bar color="white" backgroundColor="#ec6618" left-icon="" right-icon="" title="" />
            </view>
            <view class="orange-back">
                <image class="orange-back-image" src="/static/mediate/images/b-006.png"></image>
                <view class="orange-back-swiper">
                    <view class="uni-margin-wrap">
                        <swiper class="swiper swiper-index" circular :indicator-dots="false" :autoplay="true" :interval="4500" :duration="1000" :vertical="false">
                            <swiper-item>
                                <view class="swiper-item uni-bg-red">
                                    <image class="orange-back-swiper-image" src="/static/mediate/images/e-001.jpg">
                                    </image>
                                </view>
                            </swiper-item>
                        </swiper>
                    </view>
                </view>
            </view>
            <view class="mediators-list-all">
                <template v-for="item in items" :key="item.id">
                    <view class="mediators-list" :data-id="item.id" @click="jump">
                        <view class="mediators-img">
                            <image class="mediators-image" :src="item.avatar"></image>
                        </view>
                        <view class="mediators-detail">
                            <view class="mediators-detail-name">{{item.username}}</view>
                            <view class="mediators-detail-list">职务：{{item.grade.name}}</view>
                            <view class="mediators-detail-list">擅长：{{item.good_at}}</view>
                            <view class="mediators-detail-list">电话：{{item.phone}}</view>
                            <!-- <view class="mediators-detail-bottom">
								<view class="mediators-detail-more">
									<view class="mediators-detail-more-text">了解更多</view>
									<image class="mediators-detail-more-image" src="/static/mediate/images/e-003.png">
									</image>
								</view>
							</view> -->
                        </view>
                    </view>
                </template>
            </view>
        </scroll-view>
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
                items: [],
                page: 1,
                last_page: 0,
                phoneHeight: 0
            }
        },
        onShow() {
            this.getlist(true);
            this.getHeight();
        },
        methods: {
            getlist(is_super, page) {
                var _this = this;
                this.$http.get('mediate/people', {
                    page: page,
                }, result => {
                    _this.page = result.data.current_page;
                    _this.last_page = result.data.last_page;
                    _this.itemsdata = result.data.data;
                    if (is_super === true) {
                        _this.items = result.data.data;
                        _this.page = 1;
                    } else {
                        _this.items = _this.items.concat(result.data.data)
                    }
                })
            },
            bindDownLoad: function() {
                var _this = this;
                // 已经是最后一页
                if (_this.page >= _this.last_page) {
                    _this.no_more = true
                    return false;
                }
                _this.page += 1;
                _this.getlist(false, _this.page);
            },
            jump: function(e) {
                const id = e.currentTarget.dataset.id;
                // uni.navigateTo({
                // 	url: '/pages/mediate/mediator-detail?id=' + id,
                // });
            },
            getHeight: function(e) {
                var _this = this;
                uni.getSystemInfo({
                    success: function(res) {
                        if (res.safeAreaInsets.bottom) {
                            _this.phoneHeight = res.windowHeight - res.safeAreaInsets.bottom + "px";
                        } else {
                            _this.phoneHeight = res.screenHeight + "px";
                        }
                    },
                });
            }
        }
    }
</script>

<style>

</style>
