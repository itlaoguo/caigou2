import { TableProps } from "tdesign-vue-next";

// 公共配置 - 配置
export const PAGEING_ARRAY = [10, 20, 50, 100];

// 公共方法 - 按钮权限

export const checkPermission = (value: string): boolean => {
  try {
    const storedData = localStorage.getItem("BurronPer") || '[]';
    console.log("原始权限数据:", storedData); // 检查是否成功读取

    const permissions = JSON.parse(storedData) as Array<{ permission_mark: string }>;
    console.log("解析后的权限列表:", permissions); // 检查解析结果

    const hasPermission = permissions.some(item => {
      const isMatch = item.permission_mark === value;
      console.log(`权限对比: ${item.permission_mark} === ${value} → ${isMatch}`); // 逐项对比
      return isMatch;
    });

    console.log("最终权限校验结果:", hasPermission);
    return hasPermission;
  } catch (error) {
    console.error('权限数据解析失败:', error);
    return false;
  }
};

// 公共方法 - 表格数字排序方法
export const TABLE_SORT_FIGURE = async (sort: TableProps['sort'], Data: any) => {
  return new Promise((resolve) => {
    setTimeout(() => {
      let sortedData = Data.value.concat();

      if (sort && !Array.isArray(sort)) {
        sortedData = sortedData.sort((a: { [x: string]: number; }, b: { [x: string]: number; }) =>
          sort.descending ?
            b[sort.sortBy] - a[sort.sortBy] :
            a[sort.sortBy] - b[sort.sortBy]
        );
      }
      resolve(sortedData);
    }, 100);
  });
};

// 公共方法 - 表格状态筛选
type FilterConfig<T> = {
  status?: (string | number)[]; // 支持多状态筛选
  [key: string]: any; // 允许扩展其他筛选条件
};

export function TABLE_SORT_FILTER<T extends { status: any }>(
  data: T[],
  filters: FilterConfig<T>
): T[] {
  // 条件筛选
  const filtered = data.filter((item) => {
    if (!filters.status?.length) return true;
    return filters.status.includes(item.status);
  });

  // 按筛选顺序排序
  if (filters.status?.length) {
    filtered.sort((a, b) => {
      const indexA = filters.status.indexOf(a.status);
      const indexB = filters.status.indexOf(b.status);
      return indexA - indexB;
    });
  }

  return filtered;
}

// 公共方法 
// 把树形数据中有actions改成children
// 菜单页面
export function TransformActionsToChildren(tree: any[]) {
  return tree.map(node => {
    const newNode = { ...node };

    // 保留现有 children 并仅当无 children 时转换 actions
    if (!newNode.children && newNode.actions?.length) {
      newNode.children = newNode.actions;
      delete newNode.actions;
    }

    // 递归处理现有或新生成的 children
    if (newNode.children) {
      newNode.children = TransformActionsToChildren(newNode.children);
    }

    return newNode;
  });
}
