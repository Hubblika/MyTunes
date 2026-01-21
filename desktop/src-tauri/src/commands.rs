use crate::types::song::Song;
use tauri::Runtime;

#[tauri::command]
pub async fn get_song_by_uuid<R: Runtime>(app: tauri::AppHandle<R>, window: tauri::Window<R>, uuid: String) -> Option<Song> {
    Song::by_uuid(uuid).await
}
