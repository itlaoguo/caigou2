<template>
  <div class="list-common-table">

    <div class="form-item-content">
      <t-form ref="form" :data="searchParameter" :label-width="80" colon @reset="onReset" @submit="onSubmit"
        class="form-item-content">
        <t-row>
          <t-col :span="12">
            <t-row :gutter="[24, 24]">
              <t-col :xxl="2" :xl="3" :xs="6">
                <t-form-item :label="t('pages.marketingList.filterForm.namePlaceholder')" name="customer">
                  <t-input v-model="searchParameter.name" type="search"
                    :placeholder="t('pages.marketingList.filterForm.namePlaceholder')" :style="{ minWidth: '134px' }" />
                </t-form-item>
              </t-col>
              <t-col :xxl="2" :xl="3" :xs="6">
                <t-form-item :label="t('pages.marketingList.filterForm.platform')" name="platform">
                  <t-select v-model="searchParameter.platform" style="display: inline-block" :options="platforms"
                    :placeholder="t('pages.marketingList.filterForm.platformPlaceholder')" clearable />
                </t-form-item>
              </t-col>

              <t-col :xxl="6" :xl="3" :xs="0" />

              <t-col :xxl="2" :xl="3" :xs="6" class="operation-container" style="text-align: left;">
                <t-button theme="primary" type="submit" :style="{ marginLeft: 'var(--td-comp-margin-s)' }">
                  {{ t('components.commonTable.query') }}
                </t-button>
                <t-button type="reset" variant="base" theme="default"> {{ t('components.commonTable.reset') }}
                </t-button>
              </t-col>
            </t-row>
          </t-col>

        </t-row>
      </t-form>

      <t-row class="action-section">
        <t-col>
          <t-button class="action" @click="handleClickAdd()"> {{ t('pages.marketingList.actions.create') }}
          </t-button>
        </t-col>
      </t-row>

      <div class="table-container">
        <t-table :data="data" :columns="COLUMNS" :row-keys="rowKey" :pagination="pagination"
          :selected-row-keys="selectedRowKeys" :loading="dataLoading" :header-affixed-top="headerAffixedTop"
          @page-change="rehandlePageChange" @change="rehandleChange">

          <!-- 营销方式-->
          <template #methods="{ row }">

          </template>

          <!--  平台/渠道 -->
          <template #platforms="{ row }">

          </template>

          <!--  状态 -->
          <template #status="{ row }">

          </template>

          <template #op="slotProps">
            <t-space>
              <t-link theme="primary" @click="handleClickDetail(slotProps)">编辑</t-link>
              <t-link theme="primary" @click="handleClickEdit(slotProps)"> {{ t('pages.marketingList.actions.edit')
              }}</t-link>
            </t-space>
          </template>
        </t-table>

        <!--  详情 -->
        <MarketingDetail ref="marketingDetailRef" />

        <!-- 表单 -->
        <MarketingForm ref="marketingFormRef" />

      </div>
    </div>
  </div>
</template>

<script lang="ts">
export default {
  name: 'marketingList',
}
</script>

<script setup lang="ts">
import MarketingDetail from "../components/detail.vue";
import MarketingForm from "../components/form.vue";
import { computed, onMounted, ref } from 'vue';
import { useSettingStore } from '@/store';
import { prefix } from '@/config/global';
import { t } from '@/locales';
import { MessagePlugin, PageInfo, PrimaryTableCol, TableRowData, TableProps } from 'tdesign-vue-next';
import { getList as getCustomerList } from '@/api/customer/index'
import { getAll as getAllPlatforms } from '@/api/platform/index';
import { searchParameter } from "@/api/model/marketingModel";

const store = useSettingStore();


/**搜索表单*/
const searchValue = ref('');

const searchParameter: searchParameter = {
  name: '',
  platform: '',
  status: '',
  page: 1,
  pageSize: 20,
  sortBy: 'created_at',
  descending: true
}
const formData = ref<searchParameter>({ ...searchParameter });

const onReset = () => {
  formData.value = { ...searchParameter };
};

const onSubmit = () => {

};

//所有销售渠道
const platforms = ref([]);
const fetchPlatFormData = async () => {

  dataLoading.value = true;
  try {
    const result = await getAllPlatforms();
    console.log(result, '--------fetchPlatFormData---------')

    console.log("=======数据========>", result)
    if (result.code === 10000 && result.data && result.data.length > 0) {

      result.data.forEach((item: any) => {
        platforms.value.push({
          label: item.name,
          value: item.code
        })
      });
    }

  } catch (e) {
    console.log(e);
  } finally {
    dataLoading.value = false;
  }
};


/** 表格 **/
const COLUMNS: PrimaryTableCol<TableRowData>[] = [
  { colKey: 'row-select', type: 'multiple', width: 64, fixed: 'left' },
  {
    title: t('pages.marketingList.table.name'),
    align: 'left',
    width: 320,
    colKey: 'name',
    fixed: 'left',
  },
  { title: t('pages.marketingList.table.methods'), colKey: 'type', width: 160 },
  {
    title: t('pages.marketingList.table.platforms'),
    width: 160,
    ellipsis: true,
    colKey: 'total_number_of_transactions',
  },
  {
    title: t('pages.marketingList.table.status'),
    width: 160,
    ellipsis: true,
    colKey: 'total_turnover',
  },
  {
    title: t('pages.customerList.table.creator'),
    width: 160,
    ellipsis: true,
    colKey: 'creator',
  },
  {
    title: t('pages.customerList.table.createdAt'),
    width: 160,
    ellipsis: true,
    colKey: 'created_at',
    sorter: true
  },
  {
    title: t('pages.customerList.table.operate'),
    align: 'left',
    fixed: 'right',
    width: 160,
    colKey: 'op',
  },
];
const rowKey = 'index';

const headerAffixedTop = computed(
  () =>
    ({
      offsetTop: store.isUseTabsRouter ? 48 : 0,
      container: `.${prefix}-layout`,
    }) as any, // TO BE FIXED
);

const dataLoading = ref(false);
const selectedRowKeys = ref([]);
const data = ref([]);
const pagination = ref({
  defaultPageSize: 20,
  total: 0,
  defaultCurrent: 1,
});

const fetchData = async (parameters: searchParameter) => {
  // dataLoading.value = true;
  try {
    // const result = await getCustomerList(parameters);

    const result = {
      code: 10000,
      data: [
        {
          id: '1',
          name: '春季绿色大促销',
          methods: ['邮件', '短信'],
          platforms: [
            {
              id: 1,
              name: '海外商城',
            },
            {
              id: 2,
              name: '国内商城',
            },
            {
              id: 3,
              name: 'ebay',
            },
            {
              id: 4,
              name: '亚马逊',
            },
            {
              id: 5,
              name: '淘宝',
            },
          ],
          status: 1,//1待发送，2发送中，3发送完成
          creator: '刘亦菲',
          created_at: '2025-03-27 12:12:12',
        },
        {
          id: '2',
          name: '会员共庆新春',
          methods: ['邮件', '短信'],
          platforms: [
            {
              id: 1,
              name: '海外商城',
            },
            {
              id: 2,
              name: '国内商城',
            },
            {
              id: 3,
              name: 'ebay',
            },
            {
              id: 4,
              name: '亚马逊',
            },
            {
              id: 5,
              name: '淘宝',
            },
          ],
          status: 2,
          creator: '李嘉诚',
          created_at: '2025-03-27 12:12:12',
        },
      ],
      message: '操作成功'
    }

    data.value = result.data;

    pagination.value = {
      ...pagination.value,
      total: data.value.length,
    };
  } catch (e) {
    console.log(e);
  } finally {
    // dataLoading.value = false;
  }
};

/** 分页 **/
const rehandlePageChange = (pageInfo: PageInfo, newDataSource: TableRowData[]) => {
  console.log('分页变化', pageInfo, newDataSource);
};
const rehandleChange = (changeParams: unknown, triggerAndData: unknown) => {
  console.log('统一Change', changeParams, triggerAndData);
};

/** 弹窗 **/
const marketingDetailRef = ref(null)
const handleClickDetail = async (slot: { row: { id: number } }) => {
  marketingDetailRef.value.marketingDetailVisible = true;
  marketingDetailRef.value.marketingId = slot.row.id;
}

const marketingFormRef = ref(null)
const handleClickAdd = async () => {
  console.log('---marketingFormRef---')
  marketingFormRef.value.marketFormVisible = true;
  marketingFormRef.value.marketingId = 0;
}

const handleClickEdit = async (slot: { row: { id: number } }) => {
  console.log(slot.row.id)
  marketingFormRef.value.marketFormVisible = true;
  marketingFormRef.value.marketingId = slot.row.id;
}

/* 生命周期 */
onMounted(() => {
  dataLoading.value = true;
  Promise.all([fetchPlatFormData(), fetchData(searchParameter)]).then(() => {
    dataLoading.value = false;
  })

});

</script>

<style lang="less" scoped>
.action-section {
  margin-bottom: var(--td-comp-margin-xxl);
  margin-left: 15px !important;
}

.list-common-table {
  // background-color: var(--td-bg-color-container);
  padding: 15px;
  border-radius: var(--td-radius-medium);

  .table-container {
    margin-top: var(--td-comp-margin-xxl);
  }
}

.form-item-content {
  width: 100%;
  background: #fff;
  padding: 20px;
  border-radius: 10px;
}

.operation-container {
  display: flex;
  justify-content: flex-end;
  align-items: center;

  .expand {
    .t-button__text {
      display: flex;
      align-items: center;
    }
  }
}
</style>