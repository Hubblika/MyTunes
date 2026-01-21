use crate::types::FromValue;
use reqwest::{Client, Method};
use serde_json::Value;

const DOMAIN: &str = "https://mytunes.hu";

fn url(route: String) -> String {
    DOMAIN.to_string() + &route
}

pub async fn send_request(method: Method, route: String) -> Option<Value> {
    let response = Client::new()
        .request(method, url(route))
        .header("Cookie", "")
        .send()
        .await
        .ok()?;

    let value= response
        .json()
        .await
        .ok()?;

    value
}

pub async fn fetch<T>(method: Method, route: String) -> Option<T>
where
    T: FromValue
{
    let value = send_request(method, route).await?;
    let data = value.pointer("/data")?.clone();
    T::from(data)
}
