(function ($) {

  $.fn.permissionList = function (options) {
    var defaults = $.extend({
      data: '{}'
    }, options);


    var tabContent = $('<div class="tab-content"></div>'),
        permissionWrapper = $('<div class="dn-permission-wrapper col-md-12"></div>'),
        permissions = $.parseJSON(defaults.data),
        tabButtonsGroup = $('<div class="tabbable nav-tabs-vertical nav-tabs-left"></div>'),
        tabButtonsList = $('<ul class="nav nav-tabs nav-tabs-highlight"></ul>'),
        count = 1;

    this.append(permissionWrapper);
    tabButtonsGroup.append(tabButtonsList);
    permissionWrapper.append(tabButtonsGroup);

    $.each(permissions, function (moduleName, groups) {

      var btnWrapper = $('<div class="col-md-12"></div>'),
          moduleCheckInput = $('<a class="btn btn-default module-check mr-10"><i class="icon-stack-check"></i> Select All</a>'),
          moduleUncheckInput = $('<a class="btn btn-default module-uncheck"><i class="icon-stack-minus"></i> Deselect All</a>'),
          moduleCheckInput1 = $('<input type="checkbox" class="module-check">'),
          groupWrapper = $('<div class="dn-group-list"></div>'),
          tabListClass = count === 1 ? 'active' : '',
          tabPane = $('<div class="dn-module-items tab-pane has-padding '+ tabListClass +'" id="tab'+ count +'"></div>'),
          tabButtonListItem = $('<li class="dn-module-title '+ tabListClass +'"><a href="#tab'+ count +'" data-toggle="tab" class="legitRipple" aria-expanded="true">'+ moduleName +'</a></li>');
      tabButtonListItem.appendTo(tabButtonsList);

      moduleCheckInput.appendTo(btnWrapper);
      moduleUncheckInput.appendTo(btnWrapper);

      tabPane.appendTo(tabContent);
      tabContent.appendTo(tabButtonsGroup);
      btnWrapper.appendTo(tabPane);
      tabPane.append(groupWrapper);


      $.each(groups, function (groupName, perm) {
        var groupItemWrapper = $('<div class="col-md-4"></div>');
        var groupItem = $('<div class="dn-group-items"></div>');
        var groupLabel = $('<h5 class="dn-group-title"><label>' + groupName.replace(/_/, ' ') + '</label></h5>');
        var permList = $('<div class="dn-perm-list"></div>');

        var groupActionWrap = $('<span class="group-action-wrapper pull-left mr-10"></span>');
        var groupSelectAll = $('<i class="group-check cursor-pointer icon-stack-check text-muted mr-5" title="select all" ></i>');
        var groupDeselectAll = $('<i class="group-uncheck cursor-pointer icon-stack-minus text-muted" title="deselect all" ></i>');


        groupSelectAll.appendTo(groupActionWrap);
        groupDeselectAll.appendTo(groupActionWrap);
        groupActionWrap.appendTo(groupLabel);

        groupItem.append(groupLabel);
        groupItem.appendTo(groupItemWrapper);
        groupItemWrapper.appendTo(groupWrapper);

        $.each(perm, function (k, permission) {
          var permItem = $('<div class="dn-perm-item"></div>');
          var permLabel = $('<label class="dn-perm-label">' + permission.description + '</label>');
          var permCheckInput = $('<input type="checkbox" name=permissions[] class="perm-check">');
          permCheckInput.val(permission.permission);
          permCheckInput.attr('id', permission.permission);
          permCheckInput.attr('data-parent', permission.parent);
          permCheckInput.prop('checked', permission.checked);
          permLabel.prepend(permCheckInput);

          permLabel.appendTo(permItem);
          permLabel.find('label').prepend(permCheckInput);
          permItem.appendTo(permList);
        });

        permList.appendTo(groupItem);

      });

      count = count + 1;

    });

    permissionWrapper.find('.module-check').on('click', function(e){
      $(this).closest('.dn-module-items').find('input[type="checkbox"]').prop('checked', true);
    });

    permissionWrapper.find('.module-uncheck').on('click', function(e){
      $(this).closest('.dn-module-items').find('input[type="checkbox"]').prop('checked', false);
    });

    permissionWrapper.find('.group-check').on('click', function(e){
      $(this).closest('.dn-group-items').find('input[type="checkbox"]').prop('checked', true);
    });

    permissionWrapper.find('.group-uncheck').on('click', function(e){
      $(this).closest('.dn-group-items').find('input[type="checkbox"]').prop('checked', false);
    });

    permissionWrapper.find('input[type=checkbox]').on('click', function () {
      updateCheckBox(this);
    });

    var updateCheckBox = function (_obj) {
      var self = $(_obj);
      var parent = self.data('parent');
      var id = self.attr('id');

      if (parent && parent !== undefined && parent !== '' && self.prop('checked')) {
        $('#' + parent).prop('checked', true);
      }

      if (permissionWrapper.find('input[data-parent=' + id + ']').is(':checked')) {
        self.prop('checked', true);
      }

      if (!self.hasClass('group-check') && !self.hasClass('module-check')) {
        if (!self.prop('checked')) {
          self.closest('.dn-group-items').find('.group-check').prop('checked', false);
          self.closest('.dn-module-items').find('.module-check').prop('checked', false);
        } else {
          var totalCheckBox = self.closest('.dn-perm-list').find('input[type=checkbox]').length;
          var totalCheckedBox = self.closest('.dn-perm-list').find('input[type=checkbox]:checked').length;
          if (totalCheckBox == totalCheckedBox) {
            self.closest('.dn-group-items').find('.group-check').prop('checked', true);
          }
          var overallCheckBox = self.closest('.dn-group-list').find('input[type=checkbox]').length;
          var overallCheckedBox = self.closest('.dn-group-list').find('input[type=checkbox]:checked').length;
          if (overallCheckBox == overallCheckedBox) {
            self.closest('.dn-module-items').find('.module-check').prop('checked', true);
          }
        }
        return;
      }

      var wrapClass = 'dn-group-items';
      var listWrapper = 'dn-perm-list';
      if (self.hasClass('module-check')) {
        wrapClass = 'dn-module-items';
        listWrapper = 'dn-group-list';
      }
      var groupedWrapper = self.closest('.' + wrapClass).find('.' + listWrapper);
      if (self.prop('checked')) {
        groupedWrapper.find('input[type=checkbox]').prop('checked', true);
        return;
      }
      var checkBoxCount = groupedWrapper.find('input[type=checkbox]').length;
      var checkedCheckBoxCount = groupedWrapper.find('input[type=checkbox]:checked').length;
      if (checkBoxCount == checkedCheckBoxCount) {
        self.prop('checked', true);
      }


    };

    return this;
  }

}(jQuery));
