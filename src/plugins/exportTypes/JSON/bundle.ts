import { ExportTypeType } from '../../../../types/exportTypes';
import * as generator from './JSON.generator';
import { Settings } from './JSON.ui';
import Preview from './JSONPreview.component';


export const exportType: ExportTypeType = {
    generate: generator.generate,
    settingsComponent: Settings,
    previewComponent: Preview
};
