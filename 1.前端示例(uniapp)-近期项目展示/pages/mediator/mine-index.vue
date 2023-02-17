<style>
    @import "/static/mediate/css/mine-index.css";
</style>
<template>
    <view>
        <scroll-view @scrolltolower="bindDownLoad" scroll-y="true" :style="{height:phoneHeight}">
            <view class="people-back">
                <image class="back-image" src="/static/mediate/images/l-001.jpg"></image>
                <view class="head-image-left">
                    <image class="head-image" :src="avatar"></image>
                </view>
                <view class="head-image-right">
                    <view class="user-name">{{username}}</view>
                    <view class>完成调解总数：{{mediate_count}}</view>
                    <view class>平均完成时间：{{avg_time}}</view>
                    <view class>调解成功率：{{success_rate}}</view>
                    <view class>培训时长：{{training_hours}}</view>
                </view>
            </view>
            <form class="orange-back mine" @submit="formSubmit">
                <view class="search-content">
                    <input type="text" name="keyword" class="search-input" placeholder="输入手机号码 / 姓名 / 地址" placeholder-style="color:white" :value="keyword" maxlength="30">
                    <button form-type="submit" plain="true" class="search-button">
                        <image class="search-button-glass" src="/static/mediate/images/i-002.png"></image>
                    </button>
                </view>
            </form>
            <view class="mediation-list">
                <template v-for="item in items" :key="item.id">
                    <view class="mediation-content" :data-id="item.id" @click="jump">
                        <view class="mediation">
                            <view class="mediation-title">{{item.category.name}}</view>
                            <view class="evaluation">
                                <!-- 需要回访 -->
                                <view v-if="(item.workorder)&&(item.workorder.is_need_visit == 1)">
                                    <!-- 等待回访 -->
                                    <view v-if="(item.workorder)&&(item.workorder.is_need_visit == 1)&&(!item.workorder.visitlog)">
                                        <view class="evaluation-button processing">等待回访</view>
                                    </view>
                                    <!-- 已回访 -->
                                    <view v-else>
                                        <view class="evaluation-button processing">已回访</view>
                                    </view>
                                </view>
                                <!-- 不需要回访 -->
                                <view v-else>
                                    <view class="evaluation-button processing">不需要回访</view>
                                </view>
                            </view>
                        </view>
                        <view class="mediation-content-detail">
                            <view class="detail-list">调解申请：{{item.name}}</view>
                            <view class="detail-list">联系电话：{{item.mobile}} </view>
                            <view class="detail-list">发生地点：{{item.occurred_site}}</view>
                            <view class="detail-list">发生时间：{{item.occurred_time}}</view>
                            <view v-if="item.workorder.mediate_status == 1">
                                <image class="seal-image" src="/static/mediate/images/k-001.png"></image>
                            </view>
                            <view v-if="item.workorder.mediate_status == 0">
                                <image class="seal-image" src="/static/mediate/images/k-002.png"></image>
                            </view>
                            <view v-if="item.workorder.mediate_status == 3">
                                <image class="seal-image" src="/static/mediate/images/k-003.png"></image>
                            </view>
                        </view>
                    </view>
                </template>
            </view>
        </scroll-view>
        <myFoot></myFoot>
    </view>
</template>

<script>
    import request from '../../common/js/mediator-request.js';
    import myHead from "./header.vue"
    import myFoot from "./footer.vue"

    export default {
        components: {
            myHead,
            myFoot
        },
        data() {
            return {
                phoneHeight: 0,
                page: 1,
                last_page: 0,
                items: [],
                keyword: '',

                avatar: '',
                avg_time: '',
                mediate_count: 0,
                success_rate: '',
                username: '',
                training_hours: 0,
            }
        },
        onLoad(values) {
            this.keyword = values.keyword;
        },
        onShow() {
            uni.setStorageSync('mediatorFootIndex', 3);
            this.getdetail();
            this.getHeight();
        },
        methods: {
            getdetail() {
                var _this = this;
                request.get('mediator/userinfo', {

                }, result => {
                    _this.avatar = result.data.avatar;
                    _this.avg_time = result.data.avg_time;
                    _this.mediate_count = result.data.mediate_count;
                    _this.success_rate = result.data.success_rate;
                    _this.username = result.data.username;
                    _this.training_hours = result.data.training_hours;
                    _this.getlist(true);
                })
            },
            getlist(is_super, page) {
                var _this = this;
                request.get('mediator/lists', {
                    page: page,
                    status: 20,
                    keyword: this.keyword,
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
                uni.navigateTo({
                    url: '/pages/mediator/mine-record?id=' + id,
                });
            },
            formSubmit: function(e) {
                var keyword = e.detail.value.keyword;
                uni.navigateTo({
                    url: '/pages/mediator/mine-index?keyword=' + keyword,
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
