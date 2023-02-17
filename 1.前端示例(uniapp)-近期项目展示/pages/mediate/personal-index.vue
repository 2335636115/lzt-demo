<style>
    @import "/static/mediate/css/personal-index.css";
</style>
<template>
    <view>
        <scroll-view class="scrollview" @scrolltolower="bindDownLoad" scroll-y="true" :style="{height:phoneHeight}">
            <view class="orange-back">
                <image class="orange-back-image" src="/static/mediate/images/l-001.jpg"></image>
                <!-- <view class="head-portrait"> -->
                <image class="head-portrait-image" :src="avatar"></image>
                <!-- </view> -->
                <view class="nickname">{{nickname}}</view>
            </view>
            <view class="application-process">
                <view class="process-menu">
                    <view class="process-list" @click="rocess_switch(-1)">
                        <view class="list-img">
                            <image class="list-image" src="/static/mediate/images/d-003.png" v-if="mediation_open==-1"></image>
                            <image class="list-image" src="/static/mediate/images/d-003-n.png" v-else=""></image>
                        </view>
                        <text class="list-title" :class="mediation_open==-1?'active':''">全部</text>
                    </view>
                    <view class="process-list" @click="rocess_switch(0)">
                        <view class="list-img">
                            <image class="list-image" src="/static/mediate/images/d-004.png" v-if="mediation_open==0"></image>
                            <image class="list-image" src="/static/mediate/images/d-004-n.png" v-else=""></image>
                        </view>
                        <text class="list-title" :class="mediation_open==0?'active':''">待处理</text>
                    </view>
                    <view class="process-list" @click="rocess_switch(-2)">
                        <view class="list-img">
                            <image class="list-image" src="/static/mediate/images/d-005.png" v-if="mediation_open==-2"></image>
                            <image class="list-image" src="/static/mediate/images/d-005-n.png" v-else=""></image>
                        </view>
                        <text class="list-title" :class="mediation_open==-2?'active':''">处理中</text>
                    </view>
                    <view class="process-list" @click="rocess_switch(20)">
                        <view class="list-img">
                            <image class="list-image" src="/static/mediate/images/d-006.png" v-if="mediation_open==20"></image>
                            <image class="list-image" src="/static/mediate/images/d-006-n.png" v-else=""></image>
                        </view>
                        <text class="list-title" :class="mediation_open==20?'active':''">已完成</text>
                    </view>
                </view>
                <view class="mediation-list">
                    <template v-for="item in items" :key="item.id">
                        <view class="mediation-content" :data-id="item.id" :data-progress="item.progress" @click="jump">
                            <view class="mediation">
                                <view class="mediation-title">{{item.category.name}}</view>
                                <view class="evaluation">
                                    <view class="evaluation-button" v-if="item.apply_status==0">待审核</view>
                                    <template v-if="item.apply_status==10">
                                        <view class="evaluation-button" v-if="item.workorder.order_status==0">待处理</view>
                                        <view class="evaluation-button" v-if="item.workorder.order_status==10">处理中</view>
                                        <view class="evaluation-button" v-if="item.workorder.order_status==15">跟进中</view>
                                        <view class="evaluation-button" v-if="item.workorder.order_status==20">已完成</view>
                                    </template>
                                    <view class="evaluation-button" v-if="item.apply_status==90">已驳回</view>
                                </view>
                            </view>
                            <view class="mediation-content-detail">
                                <view class="detail-list">发生地点：{{item.occurred_site}} </view>
                                <view class="detail-list">发生时间：{{item.occurred_time}}</view>
                                <view class="detail-list" v-if="item.apply_status==0">当前状态：调解申请在等待审核，请留意最新动态</view>
                                <template v-if="item.apply_status==10">
                                    <view class="detail-list" v-if="item.workorder.order_status==0">当前状态：调解申请待处理中，请留意最新动态</view>
                                    <view class="detail-list" v-if="item.workorder.order_status==10">当前状态：调解申请正在处理中，请留意最新动态</view>
                                    <view class="detail-list" v-if="item.workorder.order_status==15">当前状态：调解申请正在跟进中，请留意最新动态</view>
                                    <view class="detail-list" v-if="item.workorder.order_status==20">
                                        <template v-if="item.workorder.evaluate">
                                            当前状态：调解已完成，已评价
                                        </template>
                                        <template v-else>
                                            当前状态：调解已完成，请确认并评价
                                            <view class="mediation-content-signature-title">点击评价</view>
                                        </template>
                                    </view>
                                </template>
                                <view class="detail-list" v-if="item.apply_status==90">当前状态：调解申请已驳回，请点击查看原因</view>
                            </view>
                        </view>
                    </template>
                </view>
            </view>
        </scroll-view>
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
                nickname: '',
                avatar: '',
                mediation_open: -1,
                items: [],
                page: 1,
                last_page: 0,
                phoneHeight: 0
            }
        },
        onShow() {
            uni.setStorageSync('footIndex', 4);
            this.getdetail();
            this.getHeight();
        },
        methods: {
            getdetail() {
                var _this = this;
                this.$http.get('mediate/user', {

                }, result => {
                    _this.nickname = result.data.nickname;
                    _this.avatar = result.data.avatar;
                    _this.getlist(true, 1);
                })
            },
            getlist(is_super, page) {
                var _this = this;
                this.$http.get('mediate/apply_list', {
                    status: _this.mediation_open,
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
            rocess_switch: function(e) {
                this.switch = e;
                this.mediation_open = e;
                this.getlist(true, 1);
            },
            jump: function(e) {
                const id = e.currentTarget.dataset.id;
                const progress = e.currentTarget.dataset.progress;
                uni.navigateTo({
                    url: '/pages/mediate/personal-results?id=' + id,
                });
            },
            getHeight: function(e) {
                var _this = this;
                uni.getSystemInfo({
                    success: function(res) {
                        if (res.safeAreaInsets.bottom) {
                            _this.phoneHeight = res.windowHeight - res.safeAreaInsets.bottom - 48 + "px";
                        } else {
                            _this.phoneHeight = res.screenHeight - 48 + "px";
                        }
                    },
                });
            }
        }
    }
</script>

<style>

</style>
