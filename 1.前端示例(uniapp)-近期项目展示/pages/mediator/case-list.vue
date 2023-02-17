<style>
    @import "/static/mediate/css/case-list.css";
</style>
<template>
    <view>
        <scroll-view class="application-process" @scrolltolower="bindDownLoad" scroll-y="true" :style="{height:phoneHeight}">
            <view class="mediation-list">
                <template v-for="item in items" :key="item.id">
                    <view v-if="(item.workorder)&&(item.workorder.order_status == 0)">
                        <view class="mediation-content" :data-id="item.id" @click="jump">
                            <view class="mediation">
                                <view class="evaluation-category">{{item.category.name}}</view>
                                <view class="evaluation">
                                    <template v-if="item.tag">
                                        <view class="evaluation-button tag-a" :style="'background-color:'+item.tag.color">{{item.tag.name}}</view>
                                    </template>
                                    <view class="evaluation-button ing">未受理</view>
                                </view>
                            </view>
                            <view class="mediation-content-detail">
                                <view class="mediation-title">{{item.detail_title}}</view>
                                <view class="detail-list">调解申请：{{item.name}}</view>
                                <view class="detail-list">联系电话：{{item.mobile}} </view>
                                <view class="detail-list">发生地点：{{item.occurred_site}}</view>
                                <view class="detail-list">发生时间：{{item.occurred_time}}</view>
                            </view>
                            <view class="forecast-time">
                                <template v-if="item.count_down>0">受理时间还剩<text class="time-text">{{item.count_down}}</text>天</template>
                                <template v-else>已超过考核时间</template>
                            </view>
                        </view>
                    </view>
                    <view v-else>
                        <view class="mediation-content-two">
                            <view class="mediation">
                                <view class="evaluation-category">{{item.category.name}}</view>
                                <view class="evaluation">
                                    <template v-if="item.tag">
                                        <view class="evaluation-button tag-a" :style='"background:"+item.tag.color'>{{item.tag.name}}</view>
                                    </template>
                                    <view class="evaluation-button follow">处理中</view>
                                </view>
                            </view>
                            <view class="mediation-content-detail">
                                <view class="mediation-title">{{item.detail_title}}</view>
                                <view class="detail-list">调解申请：{{item.name}}</view>
                                <view class="detail-list">联系电话：{{item.mobile}} </view>
                                <view class="detail-list">发生地点：{{item.occurred_site}}</view>
                                <view class="detail-list">发生时间：{{item.occurred_time}}</view>
                            </view>
                            <view class="forecast-time">
                                <template v-if="item.count_down>0">结束时间还剩<text class="time-text">{{item.count_down}}</text>天</template>
                                <template v-else>已超过考核时间</template>
                            </view>
                            <view class="mediation-content-detail-two">
                                <view class="mediation-content-signature-titles" :data-id="item.id" @click="jump_write">
                                    填写事件进度
                                </view>
                                <view class="mediation-content-signature-titles" :data-id="item.id" @click="jump_achieve">
                                    结案
                                </view>
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
                page: 1,
                last_page: 0,
                phoneHeight: 0,
                items: [],
                tag: '',
            }
        },
        onShow() {
            uni.setStorageSync('mediatorFootIndex', 1);
            this.getlist(true);
            this.getHeight();
        },
        methods: {
            getlist(is_super, page) {
                var _this = this;
                request.get('mediator/lists', {
                    page: page,
                    status: -1
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
                    url: '/pages/mediator/case-detail-miss?id=' + id,
                });
            },
            jump_write: function(e) {
                const id = e.currentTarget.dataset.id;
                uni.navigateTo({
                    url: '/pages/mediator/case-detail-ing?id=' + id,
                });
            },
            jump_achieve: function(e) {
                const id = e.currentTarget.dataset.id;
                uni.navigateTo({
                    url: '/pages/mediator/case-detail-review?id=' + id,
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
