<style>
    @import "/static/mediate/css/report-index.css";
</style>
<template>
    <view>
        <view class="orange-back orange-back-apply">
            <form class="orange-back-form" @submit="submit">
                <view class="form-text">申请人信息</view>
                <view class="form-text-back">
                    <view class="form-input">
                        <text class="form-input-text">姓名：</text>
                        <input class="uni-input" name="name" placeholder="请填写" maxlength="15" @input="inputname" />
                    </view>
                    <view class="form-input">
                        <text class="form-input-text">电话：</text>
                        <input class="uni-input" name="mobile" placeholder="请填写" maxlength="15" @input="inputmobile" />
                    </view>
                    <view class="form-input" style="border:none">
                        <text class="form-input-text">身份证号：</text>
                        <input class="uni-input" name="idcard" placeholder="请填写" maxlength="20" @input="inputidcard" />
                    </view>
                </view>
                <view class="form-text">纠纷描述</view>
                <view class="form-text-back">
                    <view class="form-input">
                        <text class="form-input-text">纠纷类型：</text>
                        <picker name="category_id" @change="bindPickerChange" :value="index" :range="category" range-key="name">
                            <view class="uni-input">{{category_name}}</view>
                        </picker>
                    </view>
                    <view class="form-input">
                        <text class="form-input-text">发生时间：</text>
                        <picker name="occurred_time" mode="date" :value="date" :start="startDate" :end="endDate" @change="bindDateChange">
                            <view class="uni-input">{{date}}</view>
                        </picker>
                    </view>
                    <view class="form-textarea">
                        <text class="form-input-text">发生地点：</text>
                        <textarea class="form-input-textarea" name="occurred_site" @input="inputoccurred_site" placeholder-style="color:#4F4F4F" placeholder="请填写" auto-height />
                        <view class="orange-back-input-border"></view>
                    </view>
                    <view class="form-textarea">
                        <text class="form-input-text">过程描述：</text>
                        <textarea class="form-input-textarea" name="description" @input="inputdescription" placeholder-style="color:#4F4F4F" placeholder="请填写" auto-height />
                        <view class="orange-back-input-border"></view>
                    </view>
                    <view class="form-textarea">
                        <text class="form-input-text">期望结果：</text>
                        <textarea class="form-input-textarea" name="outcome" @input="inputoutcome" placeholder-style="color:#4F4F4F" placeholder="请填写" auto-height />
                        <view class="orange-back-input-border"></view>
                    </view>
                    <view class="form-textarea">
                        <text class="form-input-text">资料上传：</text>
                        <view class="form-upload-img">
                            <uni-file-picker class="choosePicture" limit="4" file-mediatype="all" title="" mode="grid" @select="select" @delete="deletefile">
                            </uni-file-picker>
                        </view>
                    </view>
                </view>
                <button form-type="submit" :class="specClasstwo" :disabled="isDisable">上报</button>
            </form>
        </view>
        <view class="status-success" :class="tipClass">
            <view class="success-tip">
                <view class="success-tip-img">
                    <image class="success-tip-image" src="/static/mediate/images/c-012.png" mode=""></image>
                </view>
                <view class="success-tip-title">上报成功</view>
                <view class="success-tip-content">您上报的事件已申请成功，我们将会尽快开 展工作，请及时留意最新动态。</view>
                <button class="success-tip-button" @click="close_tip">确定</button>
            </view>
        </view>
        <myFoot></myFoot>
    </view>
</template>

<script>
    import request from '../../common/js/mediator-request.js';
    import myHead from "./header.vue"
    import myFoot from "./footer.vue"
    import fileUpload from "../mediate/apply-file-upload.vue"

    export default {
        components: {
            myHead,
            myFoot,
        },
        data() {
            const currentDate = this.getDate({
                format: true
            })
            return {
                name: '',
                mobile: '',
                idcard: '',
                occurred_site: '',
                description: '',
                outcome: '',
                object_news_front: '',
                object_news_back: '',

                isDisable: !1,

                object_news: [],
                tipClass: 'hide',
                array: [],
                category: [],
                category_name: '',
                index: 0,
                date: currentDate,
                imageStyles: {
                    width: 64,
                    height: 64,
                    border: {
                        radius: '50%'
                    }
                },
                listStyles: {
                    border: true, // 是否显示边框					
                    dividline: true, // 是否显示分隔线		
                    borderStyle: { // 线条样式
                        width: 1,
                        color: 'blue',
                        style: 'dashed',
                        radius: 2
                    }
                },
            }
        },
        onShow() {
            uni.setStorageSync('mediatorFootIndex', 2);
            this.get_setting();
        },
        computed: {
            startDate() {
                return this.getDate('start');
            },
            endDate() {
                return this.getDate('end');
            },
            specClasstwo() {
                let status = this.name == '' || this.mobile == '' || this.idcard == '' || this.occurred_site == '' || this.description == '' || this.outcome == '';
                return status ? 'form-button-light' : 'form-button';
            }
        },
        methods: {
            inputname(e) {
                this.name = e.detail.value;
            },
            inputmobile(e) {
                this.mobile = e.detail.value;
            },
            inputidcard(e) {
                this.idcard = e.detail.value;
            },
            inputoccurred_site(e) {
                this.occurred_site = e.detail.value;
            },
            inputdescription(e) {
                this.description = e.detail.value;
            },
            inputoutcome(e) {
                this.outcome = e.detail.value;
            },
            get_setting() {
                var _this = this;
                request.get('mediator/add_setting', {

                }, result => {
                    _this.category = result.data.category;
                    _this.category_name = _this.category[_this.index]['name'];
                })
            },
            bindPickerChange: function(e) {
                this.index = e.detail.value;
                this.category_name = this.category[this.index]['name']
            },
            bindDateChange: function(e) {
                this.date = e.detail.value
            },
            getDate(type) {
                const date = new Date();
                let year = date.getFullYear();
                let month = date.getMonth() + 1;
                let day = date.getDate();

                if (type === 'start') {
                    year = year - 20;
                } else if (type === 'end') {
                    year = year + 2;
                }
                month = month > 9 ? month : '0' + month;
                day = day > 9 ? day : '0' + day;
                return `${year}-${month}-${day}`;
            },
            showMsg: function(msg) {
                uni.showToast({
                    title: msg,
                    icon: 'error',
                    duration: 2000
                });
                this.isDisable = !1;
            },
            submit: function(e) {
                let that = this;
                if ((this.name) && (this.mobile) && (this.idcard) && (this.occurred_site) && (this.description) && (this.outcome)) {
                    this.isDisable = !0;
                    const file_all = this.object_news;
                    var files = "";
                    for (let a in file_all) {
                        if (!files) {
                            files = files + file_all[a];
                        } else {
                            files = files + "|" + file_all[a];
                        }
                    }
                    const formDetail = e.detail.value;
                    const name = formDetail.name;
                    const mobile = /^1[^012]\d{9}$/.test(formDetail['mobile']);
                    const idcard = /^[1-9]\d{5}(18|19|20|(3\d))\d{2}((0[1-9])|(1[0-2]))(([0-2][1-9])|10|20|30|31)\d{3}[0-9Xx]$/.test(formDetail['idcard']);
                    const category_id = formDetail.category_id;
                    const occurred_time = formDetail.occurred_time;
                    const occurred_site = formDetail.occurred_site;
                    const description = formDetail.description;
                    const outcome = formDetail.outcome;
                    if (!name) {
                        this.showMsg('请填写姓名');
                        return;
                    }
                    if (!mobile) {
                        this.showMsg('手机号码不正确');
                        return;
                    }
                    if (!idcard) {
                        this.showMsg('身份证不正确');
                        return;
                    }
                    if (!occurred_site) {
                        this.showMsg('请填写发生地点');
                        return;
                    }
                    if (!description) {
                        this.showMsg('请填写过程描述');
                        return;
                    }
                    if (!outcome) {
                        this.showMsg('请填写期望结果');
                        return;
                    }
                    request.post('mediator/add_save', {
                        name: formDetail.name,
                        mobile: formDetail.mobile,
                        idcard: formDetail.idcard,
                        category_id: this.category[category_id]['id'],
                        occurred_time: formDetail.occurred_time,
                        occurred_site: formDetail.occurred_site,
                        description: formDetail.description,
                        outcome: formDetail.outcome,
                        attach_file: files,
                    }, result => {
                        if (result.code == 1) {
                            that.tipClass = "show";
                        }
                    }, res => {

                    }, res => {
                        that.isDisable = !1;
                    })
                }
            },
            close_tip: function(e) {
                uni.redirectTo({
                    url: '/pages/mediator/report-index',
                });
            },
            // 获取上传状态
            select(e) {
                let obj = {};
                let object_news = this.object_news;
                e.tempFilePaths.forEach((val, ind) => {
                    request.upload('mediator/upload', val, {}, res => {
                        const url = res.data.url; //上传文件位置	
                        obj[e.tempFiles[ind].uuid] = url;
                        const obj_all = Object.assign(obj, object_news);
                        this.object_news = obj_all;
                    })
                })
            },
            // 获取上传状态（身份证前）
            select_front(e) {
                e.tempFilePaths.forEach((val, ind) => {
                    request.upload('index/upload', val, {}, res => {
                        const url = res.data.url; //上传文件位置	
                        this.object_news_front = url;
                    })
                })
            },
            // 获取上传状态（身份证后）
            select_back(e) {
                e.tempFilePaths.forEach((val, ind) => {
                    request.upload('index/upload', val, {}, res => {
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
            // 获取上传进度
            progress(e) {
                console.log('上传进度：', e)
            },
            // 上传成功
            success(e) {
                console.log('上传成功')
            },
            // 上传失败
            fail(e) {
                console.log('上传失败：', e)
            },
            //文件从列表移除时触发
            deletefile(e) {
                const i = e.tempFile.uuid;
                const object_all = this.object_news;
                Reflect.deleteProperty(object_all, i)
                this.object_news = object_all;
            }
        }
    }
</script>

<style>

</style>
